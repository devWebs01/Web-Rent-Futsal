<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Identity;
use App\Models\PaymentRecord;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses, rules, usesFileUploads};
use function Laravel\Folio\name;
use Carbon\Carbon;

uses([LivewireAlert::class]);
usesFileUploads();

name('bookings.show');

state([
    'fullpayment' => fn() => $this->booking->total_price,
    'downpayment' => fn() => $this->booking->total_price / 2,
    'payment_method' => fn() => $this->booking->payment_method ?? '',
    'expired_at' => fn() => $this->booking->expired_at ?? '',
    'booking',

    // user
    'user' => fn() => Auth()->user(),
    'booking_id' => fn() => $this->booking->id,
    'user_name' => fn() => $this->user->name,
    'user_phone' => fn() => $this->user->phone,
    'alternative_phone' => fn() => $this->booking->alternative_phone ?? '',

    // identity
    'requires_identity_validation' => fn() => $this->booking->bookingTimes->contains(fn($item) => $item->type === 'STUDENT'),
    'identity' => fn() => $this->user->identity ?? '',
    'dob',
    'document',
]);

rules([
    'payment_method' => 'required|in:downpayment,fullpayment',
]);

$validateIdentity = function () {
    $validatedData = $this->validate([
        'dob' => 'required|date',
        'document' => 'required|file|mimes:jpg,png,pdf', // Maksimal 2MB
    ]);

    $path = $this->document->store('documents', 'public');

    Identity::create([
        'user_id' => $this->user->id,
        'dob' => $validatedData['dob'],
        'document' => $path,
    ]);

    $this->alert('success', 'Validasi identitas berhasil!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('bookings.show', ['booking' => $this->booking->id]);
};

$gap_dp = fn() => $this->booking->total_price - $this->total_downpayment;

$save_booking = function () {
    $this->validate();
    $booking = $this->booking;

    if ($this->requires_identity_validation && !Identity::where('user_id', $this->user->id)->exists()) {
        $this->alert('error', 'Silakan lengkapi validasi identitas terlebih dahulu.', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    $validate_payment = $this->validate([
        'booking_id' => 'required|exists:bookings,id', // Memastikan booking_id ada di tabel bookings
        'user_name' => 'required|string|max:255', // Nama pengguna harus diisi, berupa string, dan maksimal 255 karakter
        'user_phone' => 'required|numeric', // Nomor telepon pengguna harus diisi dan berupa angka
        'alternative_phone' => 'nullable|numeric', // Nomor telepon alternatif bersifat opsional dan harus berupa angka
    ]);

    DB::beginTransaction();
    try {
        $booking->update([
            'payment_method' => $this->payment_method,
            'alternative_phone' => $this->alternative_phone,
        ]);

        $payment = Payment::create($validate_payment);

        if ($this->payment_method == 'fullpayment') {
            PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'order_id' => rand(),
            ]);
        } else {
            PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'order_id' => rand(),
            ]);

            PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'order_id' => rand(),
            ]);
        }

        DB::commit();

        $this->alert('success', 'Data booking sedang di proses!', [
            'position' => 'center',
            'timer' => 5000,
            'toast' => true,
        ]);

        $this->redirectRoute('bookings.show', ['booking' => $this->booking->id]);
    } catch (\Throwable $th) {
        DB::rollback();
        $this->alert('error', 'Ada yang salah pada input data!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

$cancelBooking = function () {
    $booking = $this->booking;
    $booking->update([
        'status' => 'CANCEL',
    ]);

    $this->alert('warning', 'Booking telah dibatalkan!', [
        'position' => 'center',
        'timer' => 5000,
        'toast' => true,
    ]);

    $this->redirectRoute('bookings.index');
};

$getTimeRemainingAttribute = function () {
    $now = Carbon::now();
    $expiry = Carbon::parse($this->expired_at);

    if ($expiry->isPast()) {
        return 'Expired';
    }

    $diffInSeconds = $expiry->diffInSeconds($now);
    $minutes = floor($diffInSeconds / 60);
    $seconds = $diffInSeconds % 60;

    return "{$minutes}m {$seconds}s";
};

?>

<x-guest-layout>

    @include('layouts.fancybox')
    @volt
        <div class="container-fluid px-3">
            <x-slot name="title">Booking {{ $booking->invoice }}</x-slot>

            @if (empty($booking->payment->records))

                <div class="mb-3">
                    @if ($requires_identity_validation)
                        <div class="card mt-3 bg-light">
                            <div class="card-body">
                                @if (empty($identity))
                                    <h5 class="mb-3 fw-bold">Validasi Identitas</h5>
                                    <form wire:submit="validateIdentity">
                                        <div class="mb-3">
                                            <label for="dob" class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" wire:model="dob" id="dob">
                                            @error('dob')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="document" class="form-label">Unggah Dokumen (Kartu Pelajar)</label>
                                            <input type="file" class="form-control" wire:model="document" id="document">
                                            @error('document')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                    </form>
                                @else
                                    <div class="row justify-content-between">
                                        <div class="col-md-5">
                                            <h5 class="mb-3 fw-bold">Profil Pelanggan</h5>
                                            <div class="pb-3">
                                                <p class="small mb-0">Nama Lengkap</p>
                                                <p class="h6">{{ $user->name }}</p>
                                                <p class="small mb-0">Tanggal Lahir</p>
                                                <p class="h6">{{ Carbon::parse($user->identity->dob)->format('d-m-Y') }}
                                                </p>
                                                <p class="small mb-0">Email</p>
                                                <p class="h6">{{ $user->email }}</p>
                                                <p class="small mb-0">Telepon</p>
                                                <p class="h6">{{ $user->phone }}</p>
                                                <p class="small mb-0">Mendaftar Pada</p>
                                                <p class="h6">
                                                    {{ Carbon::parse($user->created_at)->format('d-m-Y h:i:s') }}</p>

                                                <a class="icon-link" href="{{ route('profile.guest') }}">
                                                    Edit Profile ->
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-7 text-md-end">
                                            <h5 class="mb-3 fw-bold">Identitas Pelajar</h5>
                                            <a href="{{ Storage::url($identity->document) }}" data-fancybox>
                                                <img src="{{ Storage::url($identity->document) }}"
                                                    class="img-fluid rounded" style="object-fit:cover; height: 300px;"
                                                    alt="ducument identity user" />
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                 <section >
                    <span class="fw-bold">Invoice</span>
                    <h4 class="display-6 fw-bold text-primary">
                        {{ $booking->invoice }}
                    </h4>
                    <p class="text-muted">
                        Silakan lanjutkan ke tahap pembayaran untuk memastikan tempat bermain Anda.
                    </p>
                </section>

                <section >
                    <div class="card">
                        <div class="row g-2">
                            <div class="col">
                                <div class="card-body mb-3">
                                    <h5 class="mb-3 fw-bold ">Pemesanan</h5>
                                    <p class="text-muted">
                                        List waktu yang telah anda pilih:
                                    </p>

                                    @foreach ($booking->bookingTimes as $item)
                                        <div class="row">
                                            <div class="col-4 mb-3">
                                                @if ($item->field->images->first()->image_path)
                                                    <img src="{{ Storage::url($item->field->images->first()->image_path) }}"
                                                        class="img-fluid rounded" alt="image field">
                                                @else
                                                    <img src="https://images.pexels.com/photos/29388472/pexels-photo-29388472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                                        class="img-fluid rounded" alt="image field">
                                                @endif
                                            </div>

                                            <div class="col">
                                                <h4 class="fw-bold text-primary">{{ $item->field->field_name }}</h4>
                                                <p class="mb-0">
                                                    {{ Carbon::parse($item->booking_date)->format('d M Y') }}
                                                    - {{ $item->start_time . ' - ' . $item->end_time }}
                                                    - {{ __('type.' . $item->type) }}
                                                </p>
                                                <p>{{ formatRupiah($item->price) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-lg-5" @if (now()->lessThan(\Carbon\Carbon::parse($expired_at))) wire:poll.1s @endif>
                                <div class="card-body">
                                    <h5 class="mb-3 fw-bold">Pembayaran</h5>
                                    <div class="row mb-3">
                                        <div class="col-5">Kadaluarsa</div>
                                        <div class="col-7">
                                            : {{ $this->getTimeRemainingAttribute() }}
                                        </div>
                                        <br>
                                        <div class="col-5">Total Bayar</div>
                                        <div class="col-7">
                                            : {{ formatRupiah($booking->total_price) }}
                                        </div>
                                        <br>
                                        <div class="col-5">Status</div>
                                        <div class="col-7">
                                            : {{ __('booking.' . $booking->status) }}
                                        </div>
                                        <br>
                                        <div class="col-5">Pelanggan</div>
                                        <div class="col-7">
                                            : {{ $booking->user->name }}
                                        </div>
                                    </div>



                                    <form wire:submit='save_booking'
                                        class="{{ $booking->status !== 'CANCEL' ?: 'd-none' }}">
                                        <div class="border-top py-3">
                                            <label for="payment_method" class="form-label">
                                                Metode Pembayaran
                                            </label>
                                            <select class="form-select" wire:model.live='payment_method'
                                                name="payment_method" id="payment_method"
                                                {{ $booking->status !== 'PROCESS' ?: 'disabled' }}>
                                                <option value=" " selected>Pilih salah satu</option>
                                                <option value="downpayment">
                                                    Down Payment (DP)
                                                </option>
                                                <option value="fullpayment">
                                                    Bayar Penuh (Lunas)
                                                </option>
                                            </select>
                                            @error('payment_method')
                                                <small class="text-primary">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        @if ($payment_method === 'downpayment')
                                            <div class="mb-3">
                                                <label for="downpayment" class="form-label">Down Payment (DP)</label>
                                                <input type="number" class="form-control" name="downpayment"
                                                    id="downpayment" value="{{ $downpayment }}" readonly
                                                    {{ $booking->status !== 'PROCESS' ?: 'disabled' }} />
                                                <small id="downpaymentId" class="form-text text-muted">Silahkan bayar
                                                    sisa
                                                    pembayaran saat dilapangan</small>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label for="alternative_phone" class="form-label">Telp Alternatif
                                                (Opsional)</label>
                                            <input type="number" wire:model='alternative_phone' class="form-control"
                                                name="alternative_phone" id="alternative_phone"
                                                {{ $booking->status !== 'PROCESS' ?: 'disabled' }} />
                                            @error('alternative_phone')
                                                <small id="alternative_phoneId" class="form-text text-primary">
                                                    {{ $message }}
                                                </small>
                                            @else
                                                <small id="alternative_phoneId" class="form-text text-muted">Nomor
                                                    alternatif
                                                    yang
                                                    dapat dihubungi.</small>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <button type="button" wire:click='cancelBooking'
                                                    class="w-100 btn btn-dark {{ $booking->status !== 'PROCESS' ?: 'd-none' }}">
                                                    Batal
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="submit"
                                                    class="w-100 btn btn-primary {{ $booking->status !== 'PROCESS' ?: 'd-none' }}">
                                                    Submit
                                                </button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <div >
                    @include('pages.guest.bookings.invoice', ['booking' => $booking])
                </div>
            @endif

        </div>
    @endvolt
</x-guest-layout>
