<?php

use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name("transactions.show");

state([
    "user" => fn() => $this->booking->user,
    "payment" => fn() => $this->booking->payment ?? null,
    "records" => fn() => $this->booking->payment->records ?? null,
    "fullpayment" => fn() => $this->booking->total_price,
    "downpayment" => fn() => $this->booking->total_price / 2,
    "requires_identity_validation" => fn() => $this->booking->bookingTimes->contains(fn($item) => $item->type === "STUDENT"),
    "booking",
    "id",
]);

$confirmBooking = function () {
    try {
        // Cek apakah payment ada dan records tidak null
        if (!$this->payment || !$this->records) {
            $this->alert("error", "Metode pembayaran belum dipilih atau data pembayaran tidak tersedia.", [
                "position" => "center",
            ]);
            return;
        }

        $this->booking->update([
            "status" => "CONFIRM",
        ]);

        $this->alert("success", "Proses berhasil!", [
            "position" => "center",
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal!", [
            "position" => "center",
        ]);
    }
};

$cancelBooking = function () {
    try {
        $this->booking->update([
            "status" => "CANCEL",
        ]);

        $this->alert("success", "Proses berhasil!", [
            "position" => "center",
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal!", [
            "position" => "center",
        ]);
    }
};

$cashPayment = function ($id) {
    try {
        $record = PaymentRecord::findOrFail($id);
        $record->update([
            "status" => "PAID",
            "gross_amount" => $this->booking->payment_method === "fullpayment" ? $this->fullpayment : $this->downpayment,
            "payment_time" => now(),
            "payment_type" => "CASH",
            "payment_detail" => "Pembayaran ditempat langsung",
            "status_message" => "Pembayaran telah dilakukan",
        ]);

        // → JIKA INGIN AUTO-CONFIRM BOOKING KETIKA SEMUA RECORD SUDAH PAID
        $undone = $this->booking->payment->records()->where("status", "DRAF")->count();
        if ($undone === 0) {
            $this->booking->update(["status" => "CONFIRM"]);
        }

        $this->redirectRoute("transactions.show", ["booking" => $this->booking->id]);

        $this->alert("success", "Proses Berhasil!", [
            "position" => "center",
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal: " . $th->getMessage(), [
            "position" => "center",
        ]);
    }
};

?>

<x-admin-layout>
    <x-slot name="title">Detail Transaksi</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route("home") }}">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route("transactions.index") }}">Transaksi</a>
        </li>
        <li class="breadcrumb-item">
            <a href="#">Invoice</a>
        </li>
    </x-slot>

    @include("layouts.fancybox")

    @volt
        <div>

            @if ($requires_identity_validation)
                <div class="alert alert-danger" role="alert">
                    <strong>Pesan Penting</strong>
                    <p>Penyewaan ini membutuhkan konfirmasi lebih lanjut tentang harga dan identitas penyewa. Lakukan
                        pengecekan identitas pelajar sebelum mengkonfimasi. </p>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $booking->status !== "VERIFICATION" ? "active" : "" }}"
                                id="pills-invoice-tab" data-bs-toggle="pill" data-bs-target="#pills-invoice" type="button"
                                role="tab" aria-controls="pills-invoice"
                                aria-selected="{{ $booking->status !== "VERIFICATION" ? "true" : "false" }}">
                                Data Pemesanan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $booking->status === "VERIFICATION" ? "active" : "" }}"
                                id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button"
                                role="tab" aria-controls="pills-profile"
                                aria-selected="{{ $booking->status === "VERIFICATION" ? "true" : "false" }}">
                                Data Pelanggan
                            </button>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="tab-content p-0 mt-5" id="pills-tabContent">
                <div class="tab-pane fade  {{ $booking->status !== "VERIFICATION" ? "show active" : "" }}"
                    id="pills-invoice" role="tabpanel" aria-labelledby="pills-invoice-tab" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            @include("pages.admin.transactions.action")

                            <div class="row">
                                <small class="h5 fw-bold">
                                    Pembayaran
                                </small>
                                @if ($payment && $payment->records)
                                    @foreach ($payment->records as $item)
                                        <div class="col-md mb-5">
                                            <div class="card text-start mb-3 h-100">
                                                <div class="card-body">
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            Status
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            {{ __("record." . $item->status) }}
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
                                                            {{ formatRupiah($item->gross_amount) ?? "-" }}
                                                        </div>
                                                        <div class="col-6">
                                                            Waktu pembayaran
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            {{ $item->payment_time ?? "-" }}
                                                        </div>
                                                        <div class="col-6">
                                                            Jenis pembayaran
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            {{ $item->payment_type ?? "-" }}
                                                        </div>
                                                        <div class="col-6">
                                                            Detail pembayaran
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            {{ $item->payment_detail ?? "-" }}
                                                        </div>
                                                        <div class="col-6">
                                                            Pesan pembayaran
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            {{ $item->status_message ?? "-" }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="card-footer {{ in_array($booking->status, ["UNPAID", "PROCESS"]) ? "" : "d-none" }}">
                                                    @if ($item->status === "DRAF" || $item->status === "UNPAID")
                                                        <a wire:click="cashPayment({{ $item->id }})"
                                                            class="w-100 btn btn-primary" href="#" role="button">
                                                            Bayar ditempat
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning">
                                        Data pembayaran tidak tersedia.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer {{ $booking->status === "PROCESS" ?: "d-none" }}">
                            <div class="row">
                                <div class="col-md">
                                    <button wire:click='cancelBooking' class="btn btn-danger w-100">
                                        Batalkan
                                    </button>
                                </div>
                                {{-- <div class="col-md">
                                    <button wire:click='confirmBooking' class="btn btn-primary w-100">
                                        Konfirmasi
                                    </button>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ $booking->status === "VERIFICATION" ? "show active" : "" }}"
                    id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">

                    <div class="card">
                        <div class="card-body">
                            @include("pages.admin.transactions.profile-customer", ["booking" => $booking])

                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endvolt
</x-admin-layout>
