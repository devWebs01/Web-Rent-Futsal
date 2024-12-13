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

name('bookings.checkout');

state([
    'fullpayment' => fn() => $this->booking->total_price,
    'downpayment' => fn() => $this->booking->total_price / 2,
    'payment_method',
    'booking',

    //
    'user' => fn() => Auth()->user(),
    'booking_id' => fn() => $this->booking->id,
    'user_name' => fn() => $this->user->name,
    'user_phone' => fn() => $this->user->phone,
    'phone_alternative',
]);

rules([
    'payment_method' => 'required|in:downpayment,fullpayment',
]);

//  // Booking
//  'PAID' => 'Bayar',
//  'UNPAID' => 'Belum Bayar',
//  'DOWNPAYMENT' => 'Down Payment (DP)',
//  'CANCEL' => 'Batal',
//  'PROCESS' => 'Sedang Diproses',

//  // PaymentRecord
//  'DRAF' => 'Draf',
//  'WAITING' => 'Menunggu Konfirmasi',
//  'CONFIRM' => 'Konfirmasi',
//  'REJECT' => 'Tolak',

$gap_dp = fn() => $this->booking->total_price - $this->total_downpayment;

$save_booking = function () {
    $this->validate();

    $validate_payment = $this->validate([
        'booking_id' => 'required|exists:bookings,id', // Memastikan booking_id ada di tabel bookings
        'user_name' => 'required|string|max:255', // Nama pengguna harus diisi, berupa string, dan maksimal 255 karakter
        'user_phone' => 'required|numeric', // Nomor telepon pengguna harus diisi dan berupa angka
        'phone_alternative' => 'nullable|numeric', // Nomor telepon alternatif bersifat opsional dan harus berupa angka
    ]);

    DB::beginTransaction();
    try {
        $payment = Payment::create($validate_payment);

        if ($this->payment_method == 'fullpayment') {
            PaymentRecord::create([
                'payment_id' => $payment->id,
                'status' => 'DRAF',
                'amount' => $this->fullpayment,
            ]);
        } else {
            PaymentRecord::create([
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

        $this->alert('success', 'Data booking sedang di proses! Silahkan lanjut pada upload bukti pembayaran', [
            'position' => 'center',
            'timer' => 5000,
            'toast' => true,
        ]);

        $this->redirectRoute('bookings.index');
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
    <x-slot name="title">Edit booking</x-slot>


    @volt
        <div>
            <section class="container p-0 text-center">
                <span class="fw-bold">Checkout</span>
                <h4 class="display-6 fw-bold text-danger">
                    {{ $booking->invoice }}
                </h4>
                <p class="text-muted">
                    Silakan lanjutkan ke tahap pembayaran untuk memastikan tempat bermain Anda.
                </p>
            </section>

            <section>
                <div class="container">
                    <div class="row text-center mt-5 px-0 py-3">
                        <div class="col">
                            <h6 class="text-muted">Total Bayar</h6>
                            <h4 class="fw-bold text-danger">
                                {{ formatRupiah($booking->total_price) }}
                            </h4>
                        </div>
                        <div class="col">
                            <h6 class="text-muted">Status</h6>
                            <h4 class="fw-bold text-danger">
                                {{ __('status.' . $booking->status) }}
                            </h4>
                        </div>
                        <div class="col">
                            <h6 class="text-muted">Pelanggan</h6>
                            <h4 class="fw-bold text-danger">
                                {{ $booking->user->name }}
                            </h4>
                        </div>
                    </div>
                </div>
            </section>


            <section class="py-5">
                <div class="container">
                    <div class="row g-5">
                        <div class="col-lg-7">
                            <div class="text-center text-lg-start">
                                <h4 class="mb-0 fw-bold">Detail Waktu Main</h4>
                                <p class="text-muted">
                                    List Waktu yang Telah Anda Pilih:
                                </p>
                            </div>
                            <div class="table-responsive border rounded-4">
                                <table class="table table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th>Lapangan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Tipe</th>
                                            <th>Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($booking->times as $item)
                                            <tr>
                                                <td>{{ $item->field->field_name }}</td>
                                                <td>{{ Carbon::parse($item->booking_date)->format('d M Y') }}</td>
                                                <td>{{ $item->start_time . ' - ' . $item->end_time }}</td>
                                                <td>{{ __('type.' . $item->type) }}</td>
                                                <td>{{ formatRupiah($item->price) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="text-center text-lg-start">
                                <h4 class="mb-0 fw-bold">Pembayaran</h4>
                                <p class="text-muted">
                                    Upload bukti pembayaran dan pilih metode pembayaran.
                                </p>
                            </div>
                            <form wire:submit='save_booking'>
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">
                                        Metode Pembayaran
                                    </label>
                                    <select class="form-select" wire:model.live='payment_method' name="payment_method"
                                        id="payment_method">
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
                                        <input type="number" class="form-control" name="downpayment" id="downpayment"
                                            value="{{ $downpayment }}" readonly />
                                        <small id="downpaymentId" class="form-text text-muted">Silahkan bayar sisa
                                            pembayaran saat dilapangan</small>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="phone_alternative" class="form-label">Telp Alternatif (Opsional)</label>
                                    <input type="number" class="form-control" name="phone_alternative"
                                        id="phone_alternative" />
                                    @error('phone_alternative')
                                        <small id="phone_alternativeId" class="form-text text-danger">
                                            {{ $message }}
                                        </small>
                                    @else
                                        <small id="phone_alternativeId" class="form-text text-muted">Nomor alternatif yang
                                            dapat dihubungi.</small>
                                    @enderror
                                </div>

                                <button type="submit" class="w-100 btn btn-danger">
                                    Submit
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </section>


        </div>
    @endvolt
</x-guest-layout>
