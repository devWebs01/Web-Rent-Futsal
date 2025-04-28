<?php

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use function Livewire\Volt\{state, uses, usesFileUploads};
use function Laravel\Folio\{name};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

usesFileUploads();

name("payment_record.show");

state([
    "booking" => fn() => $this->paymentRecord->payment->booking,
    "payment" => fn() => $this->paymentRecord->payment,
    "expired_at" => fn() => $this->booking->expired_at ?? "",
    "fullpayment" => fn() => $this->booking->total_price,
    "downpayment" => fn() => $this->booking->total_price / 2,
    "snapToken" => fn() => $this->paymentRecord->snapToken ?? "",
    "receipt",
    "paymentRecord",
]);

$getTimeRemainingAttribute = function () {
    $now = Carbon::now();
    $expiry = Carbon::parse($this->expired_at);

    if ($expiry->isPast()) {
        return "Expired";
    }

    $diffInSeconds = $expiry->diffInSeconds($now);
    $minutes = floor($diffInSeconds / 60);
    $seconds = $diffInSeconds % 60;

    return "{$minutes}m {$seconds}s";
};

// … header dan imports tetap sama …

$updateStatus = function () {
    Config::$serverKey = config("midtrans.server_key");
    Config::$isProduction = config("midtrans.is_production");
    Config::$isSanitized = true;
    Config::$is3ds = true;

    try {
        $response = \Midtrans\Transaction::status($this->paymentRecord->order_id);

        $booking = $this->booking;
        if (!$booking) {
            Log::warning("Booking tidak ditemukan untuk order_id: {$response->order_id}");
            return;
        }

        // ─── 1) Tentukan paymentStatus ───────────────────────────────────────────
        $mapping = [
            "capture" => "PAID",
            "settlement" => "PAID",
            "pending" => "PROCESS", // default
            "deny" => "FAILED",
            "cancel" => "FAILED",
            "expire" => "FAILED",
            "challenge" => "VERIFICATION",
        ];
        // override: treat sandbox pending as PAID
        if ($response->transaction_status === "pending") {
            $paymentStatus = "PAID";
        } else {
            $paymentStatus = $mapping[$response->transaction_status] ?? "PROCESS";
        }

        // ─── 2) Update satu‐satu PaymentRecord yang diproses sekarang ─────────────
        $detail = match ($response->payment_type) {
            "credit_card" => "Bank: {$response->bank}, Kartu: {$response->card_type}",
            "bank_transfer" => optional($response->va_numbers[0], fn($va) => "Bank: {$va->bank}, VA: {$va->va_number}") ?: "bank_transfer",
            "cstore" => $response->store,
            default => $response->payment_type,
        };

        $this->paymentRecord->update([
            "status" => $paymentStatus,
            "status_message" => $response->status_message,
            "gross_amount" => $response->gross_amount,
            "payment_time" => $response->settlement_time ?? $response->transaction_time,
            "payment_type" => $response->payment_type,
            "payment_detail" => $detail,
        ]);

        // ─── 3) Hitung ulang semua PaymentRecord untuk booking ini ───────────────
        $payment = $booking->payment; // hasOne
        $records = $payment ? $payment->records : collect();
        $paidCount = $records->where("status", "PAID")->count();
        $totalCount = $records->count();

        // ─── 4) Tentukan status Booking sekali saja ──────────────────────────────
        if ($booking->payment_method === "fullpayment") {
            // fullpayment: 1 record → langsung VERIFICATION kalau terbayar
            if ($paidCount >= 1) {
                $booking->update(["status" => "CONFIRM"]);
            }
        } elseif ($booking->payment_method === "downpayment") {
            // dp: 2 record; 1 record terbayar → PROCESS; 2 terbayar → CONFIRM
            if ($paidCount === 1) {
                $booking->update(["status" => "PROCESS"]);
            } elseif ($paidCount >= $totalCount && $totalCount > 1) {
                $booking->update(["status" => "CONFIRM"]);
            }
        }

        Log::info("Booking#{$booking->id} status → {$booking->status}, PaymentRecord status → {$paymentStatus}");
        return $this->redirectRoute("bookings.show", ["booking" => $booking]);
    } catch (\Exception $e) {
        Log::error("Error cek Midtrans: " . $e->getMessage());
        $msg = $e instanceof ValidationException ? implode("<br>", $e->validator->errors()->all()) : "Terjadi kesalahan sistem. Coba lagi.";
        return $this->alert("error", "Error pengecekan Midtrans!<br>{$msg}", [
            "position" => "center",
            "timer" => 4000,
            "width" => 500,
            "toast" => true,
            "timerProgressBar" => true,
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
                                            {{ $booking->payment_method === "fullpayment" ? formatRupiah($fullpayment) : formatRupiah($downpayment) }}
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
                                                {{ __("record." . $paymentRecord->status) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah harus dibayar
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $booking->payment_method === "fullpayment" ? formatRupiah($fullpayment) : formatRupiah($downpayment) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah yang diterima
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->gross_amount ?? "-" }}
                                            </div>
                                            <div class="col-6">
                                                Waktu pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->payment_time ?? "-" }}
                                            </div>
                                            <div class="col-6">
                                                Jenis pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->payment_type ?? "-" }}
                                            </div>
                                            <div class="col-6">
                                                Detail pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->payment_detail ?? "-" }}
                                            </div>
                                            <div class="col-6">
                                                Pesan pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $paymentRecord->status_message ?? "-" }}
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

            @push("styles")
                <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
                    data-client-key="{{ config("midtrans.client_key") }}"></script>
            @endpush

            @push("scripts")
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
