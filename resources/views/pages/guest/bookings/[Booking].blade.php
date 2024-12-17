<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentRecord;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses, rules};
use function Laravel\Folio\name;
use Carbon\Carbon;

uses([LivewireAlert::class]);

name('bookings.show');

state([
    'fullpayment' => fn() => $this->booking->total_price,
    'downpayment' => fn() => $this->booking->total_price / 2,
    'payment_method' => fn() => $this->booking->payment_method ?? '',
    'booking',

    //
    'user' => fn() => Auth()->user(),
    'booking_id' => fn() => $this->booking->id,
    'user_name' => fn() => $this->user->name,
    'user_phone' => fn() => $this->user->phone,
    'alternative_phone' => fn() => $this->booking->alternative_phone ?? '',
]);

rules([
    'payment_method' => 'required|in:downpayment,fullpayment',
]);

$gap_dp = fn() => $this->booking->total_price - $this->total_downpayment;

$save_booking = function () {
    $this->validate();
    $booking = $this->booking;

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
            $record = PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'amount' => $this->fullpayment,
            ]);
        } else {
            $record = PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'amount' => $this->downpayment,
            ]);

            PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'amount' => $this->downpayment,
            ]);
        }

        DB::commit();

        $this->alert('success', 'Data booking sedang di proses!', [
            'position' => 'center',
            'timer' => 5000,
            'toast' => true,
        ]);

        $this->redirectRoute('payment_record.show', ['paymentRecord' => $record->id]);
    } catch (\Throwable $th) {
        DB::rollback();
        $this->alert('error', 'Ada yang salah pada input data!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-guest-layout>


    @volt
        <div>
            <x-slot name="title">Booking {{ $booking->invoice }}</x-slot>

            @if (empty($booking->payment->records))
                <section class="container">
                    <span class="fw-bold">Invoice</span>
                    <h4 class="display-6 fw-bold text-danger">
                        {{ $booking->invoice }}
                    </h4>
                    <p class="text-muted">
                        Silakan lanjutkan ke tahap pembayaran untuk memastikan tempat bermain Anda.
                    </p>
                </section>

                <section class="p-0">
                    <div class="container">
                        <div class="row g-2">
                            <div class="col">
                                <div class="card border-0">
                                    <div class="card-body mb-3">
                                        <h5 class="mb-3 fw-bold ">Pemesanan</h5>
                                        <hr>
                                        <div class="row">
                                            <div class="text-muted col-5">Total Bayar</div>
                                            <div class="fw-bold text-danger col-7">
                                                {{ formatRupiah($booking->total_price) }}
                                            </div>
                                            <br>
                                            <div class="text-muted col-5">Status</div>
                                            <div class="fw-bold text-danger col-7">
                                                {{ __('status.' . $booking->status) }}
                                            </div>
                                            <br>
                                            <div class="text-muted col-5">Pelanggan</div>
                                            <div class="fw-bold text-danger col-7">
                                                {{ $booking->user->name }}
                                            </div>
                                        </div>

                                        <p class="text-muted mt-4 text-lowercase">
                                            List Waktu yang Telah Anda Pilih:
                                        </p>


                                        @foreach ($booking->times as $item)
                                            <div class="row">
                                                <div class="col-4">
                                                    @if ($item->field->images->first()->image_path)
                                                        <img src="{{ Storage::url($item->field->images->first()->image_path) }}"
                                                            class="img-fluid rounded" alt="image field">
                                                    @else
                                                        <img src="https://images.pexels.com/photos/29388472/pexels-photo-29388472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                                            class="img-fluid rounded" alt="image field">
                                                    @endif
                                                </div>

                                                <div class="col">
                                                    <h4 class="fw-bold text-danger">{{ $item->field->field_name }}</h4>
                                                    <p class="small mb-0">
                                                        {{ Carbon::parse($item->booking_date)->format('d M Y') }}
                                                        - {{ $item->start_time . ' - ' . $item->end_time }}
                                                        - {{ __('type.' . $item->type) }}
                                                    </p>
                                                    <p class="small">{{ formatRupiah($item->price) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="card border-0">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">Pembayaran</h5>
                                        <hr>
                                        <form wire:submit='save_booking'>
                                            <div class="mb-3">
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
                                                    <small class="text-danger">{{ $message }}</small>
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
                                                    <small id="alternative_phoneId" class="form-text text-danger">
                                                        {{ $message }}
                                                    </small>
                                                @else
                                                    <small id="alternative_phoneId" class="form-text text-muted">Nomor
                                                        alternatif
                                                        yang
                                                        dapat dihubungi.</small>
                                                @enderror
                                            </div>

                                            <button type="submit"
                                                class="w-100 btn btn-danger {{ $booking->status !== 'PROCESS' ?: 'd-none' }}">
                                                Submit
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <div class="container">
                    @include('pages.guest.bookings.invoice')
                </div>
            @endif

        </div>
    @endvolt
</x-guest-layout>
