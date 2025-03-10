<?php

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use function Livewire\Volt\{state, uses, usesFileUploads};
use function Laravel\Folio\{name};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

usesFileUploads();

name('payment_record.show');

state([
    'booking' => fn() => $this->paymentRecord->payment->booking,
    'payment' => fn() => $this->paymentRecord->payment,
    'expired_at' => fn() => $this->booking->expired_at ?? '',
    'fullpayment' => fn() => $this->booking->total_price,
    'downpayment' => fn() => $this->booking->total_price / 2,
    'snapToken' => fn() => $this->paymentRecord->snapToken ?? '',
    'receipt',
    'paymentRecord',
]);

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

$updateStatus = function () {
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    try {
        $response = \Midtrans\Transaction::status($this->paymentRecord->order_id);

        // dd($response);

        // Temukan Booking dan Payment berdasarkan order_id
        $booking = $this->booking;
        $payment = $this->paymentRecord;

        if (!$booking) {
            Log::warning("Booking tidak ditemukan untuk order_id: {$response->order_id}");
            return;
        }

        // Mapping status Midtrans ke status Booking
        $bookingStatusMapping = [
            'capture' => 'PROCESS', // Pembayaran berhasil, siap diproses
            'settlement' => 'PROCESS', // Sudah lunas, booking selesai
            'pending' => 'PENDING', // Menunggu pembayaran
            'deny' => 'CANCEL', // Ditolak Midtrans
            'cancel' => 'CANCEL', // Dibatalkan pengguna/admin
            'expire' => 'CANCEL', // Kadaluarsa
            'challenge' => 'VERIFICATION', // Perlu verifikasi manual
        ];

        // Mapping status Midtrans ke status Payment
        $paymentStatusMapping = [
            'capture' => 'PAID', // Pembayaran berhasil
            'settlement' => 'PAID', // Sudah lunas
            'pending' => 'PROCESS', // Menunggu pembayaran
            'deny' => 'FAILED', // Pembayaran gagal
            'cancel' => 'FAILED', // Pembatalan pembayaran
            'expire' => 'FAILED', // Pembayaran kadaluarsa
            'challenge' => 'VERIFICATION', // Masih dalam pengecekan
        ];

        // Tentukan status berdasarkan response Midtrans
        $bookingStatus = $bookingStatusMapping[$response->transaction_status] ?? 'VERIFICATION'; // Default PROCESS jika status tidak dikenali
        $paymentStatus = $paymentStatusMapping[$response->transaction_status] ?? 'PROCESS'; // Default UNPAID jika status tidak dikenali

        // Update status pada Booking dan Payment
        $booking->update(['status' => $bookingStatus]);
        if ($payment) {
            if ($response->payment_type === 'credit_card') {
                $detail = 'Bank: ' . $response->bank . ', Tipe Kartu' . $response->card_type;
            } elseif ($response->payment_type === 'bank_transfer') {
                $bank = $response->va_numbers[0]->bank;
                $va_number = $response->va_numbers[0]->va_number;
                $detail = 'Bank: ' . $bank . ', VA Number: ' . $va_number;
            } elseif ($response->payment_type === 'cstore') {
                $detail = $response->store;
            } else {
                $detail = $response->payment_type;
            }

            $payment->update([
                'status' => $paymentStatus,
                'status_message' => $response->status_message,
                'gross_amount' => $response->gross_amount,
                'payment_time' => $response->settlement_time ?? $response->transaction_time,
                'payment_type' => $response->payment_type,
                'payment_detail' => $detail ?? '',
            ]);
        }

        Log::info("Booking dan Payment diperbarui: Order ID: {$response->order_id}, Booking Status: {$bookingStatus}, Payment Status: {$paymentStatus}");

        $this->redirectRoute('bookings.show', [
            'booking' => $this->booking,
        ]);
    } catch (\Exception $e) {
        Log::error('Error dalam pengecekan status Midtrans: ' . $e->getMessage());

        if ($e instanceof ValidationException) {
            $errorMessages = implode('<br>', $e->validator->errors()->all());
        } else {
            $errorMessages = 'Terjadi kesalahan pada sistem. Selesaikan pembayaran mu dan coba lagi.';
        }

        $this->alert('error', 'Error dalam pengecekan status Midtrans! <br>' . $errorMessages, [
            'position' => 'center',
            'timer' => 4000,
            'width' => 500,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }
};

?>

<x-guest-layout>
    <x-slot name="title">Detail Pambayaran</x-slot>
    @volt
        <div class="container-fluid px-3">
            <section>
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <strong>Setelah pembayaran selesai!</strong> Silakan perbarui status pembayaran dengan mengklik
                    tombol di
                    bawah ini.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </section>

            <section>
                <div class="row">
                    <div class="col-md">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 id="font-custom" style="color: #f35525">
                                    {{ $payment->payment_type }}
                                </h2>
                                <h6>Total Pembayaran:</h6>
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto">
                                        <h1 class="text-primary">
                                            {{ $booking->payment_method === 'fullpayment' ? formatRupiah($fullpayment) : formatRupiah($downpayment) }}
                                        </h1>
                                    </div>
                                    <div class="col-auto text-end">
                                        <div wire:loading class="spinner-border spinner-border-sm ms-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <div class="my-3">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                Status
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ __('record.' . $paymentRecord->status) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah harus dibayar
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $booking->payment_method === 'fullpayment' ? formatRupiah($fullpayment) : formatRupiah($downpayment) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah yang diterima
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->gross_amount ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Waktu pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->payment_time ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Jenis pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->payment_type ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Detail pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->payment_detail ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Pesan pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->status_message ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="row gap-3">
                                            <div class="col-md">
                                                <button type="button" id="pay-button" href="{{ $snapToken }}"
                                                    class="btn btn-light border w-100">
                                                    Pilih
                                                    Metode
                                                </button>
                                            </div>
                                            <div class="col-md">
                                                <button class="btn btn-outline-dark w-100" wire:click='updateStatus'>
                                                    Perbarui Status
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md">
                        <div class="card h-100">
                            <div class="card-body w-100" id="snap-container"></div>
                        </div>
                    </div>
                </div>
            </section>

            @push('styles')
                <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
                    data-client-key="{{ config('midtrans.client_key') }}"></script>
            @endpush

            @push('scripts')
                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        var payButton = document.getElementById('pay-button');

                        // Cek apakah payButton ada di DOM
                        if (!payButton) {
                            console.error("Tombol bayar tidak ditemukan!");
                            return;
                        }

                        // Debugging: Cek apakah snapToken tersedia
                        var snapToken = @json($snapToken);
                        console.log("Snap Token:", snapToken);

                        if (!snapToken) {
                            alert("Snap Token tidak tersedia, pastikan token dibuat di backend.");
                            return;
                        }

                        // Tambahkan event listener ke tombol
                        payButton.addEventListener('click', function() {
                            window.snap.embed(snapToken, {
                                embedId: 'snap-container',
                                onSuccess: function(result) {
                                    alert("Pembayaran sukses!");
                                    console.log(result);
                                    location.reload();
                                },
                                onPending: function(result) {
                                    alert("Menunggu pembayaran!");
                                    console.log(result);
                                    location.reload();
                                },
                                onError: function(result) {
                                    alert("Pembayaran gagal!");
                                    console.log(result);
                                    location.reload();
                                },
                                onClose: function() {
                                    alert('Anda menutup pembayaran sebelum selesai.');
                                }
                            });
                        });
                    });
                </script>
            @endpush
        </div>
    @endvolt
</x-guest-layout>
