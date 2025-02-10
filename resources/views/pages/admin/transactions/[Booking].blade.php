<?php

use App\Models\Booking;
use App\Models\PaymentRecord;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;
use Carbon\Carbon;

uses([LivewireAlert::class]);

name('transactions.show');

state([
    'user' => fn() => $this->booking->user,
    'invoice' => fn() => $this->booking->invoice,
    'totalPrice' => fn() => $this->booking->bookingTimes->sum('price'),
    'payment' => fn() => $this->booking->payment ?? null,
    'records' => fn() => $this->booking->payment->records ?? null,
    'fullpayment' => fn() => $this->booking->total_price,
    'downpayment' => fn() => $this->booking->total_price / 2,
    'booking',
    'id',
]);

$updatingStatusTimes = function () {
    if (!$this->booking->bookingTimes) {
        return false;
    }

    foreach ($this->booking->bookingTimes as $time) {
        $time->update([
            'status' => 'START',
        ]);
    }
};

$confirmBooking = function () {
    try {
        // Cek apakah payment ada dan records tidak null
        if (!$this->payment || !$this->records) {
            $this->alert('error', 'Metode pembayaran belum dipilih atau data pembayaran tidak tersedia.', [
                'position' => 'center',
            ]);
            return;
        }

        $this->updatingStatusTimes();

        $this->booking->update([
            'status' => 'CONFIRM',
        ]);

        $this->alert('success', 'Proses berhasil!', [
            'position' => 'center',
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagal!', [
            'position' => 'center',
        ]);
    }
};

$cancelBooking = function () {
    try {
        $this->booking->update([
            'status' => 'CANCEL',
        ]);

        $this->alert('success', 'Proses berhasil!', [
            'position' => 'center',
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagal!', [
            'position' => 'center',
        ]);
    }
};

$cashPayment = function ($id) {
    try {
        // Cari data berdasarkan ID
        $record = PaymentRecord::findOrFail($id);
        // Update status
        $record->update([
            'status' => 'PAID',
            'gross_amount' => $this->booking->payment_method === 'fullpayment' ? $this->fullpayment : $this->downpayment,
            'payment_time' => now(),
            'payment_type' => 'CASH',
            'payment_detail' => 'Pembayaran ditempat langsung',
            'status_message' => 'Pembayaran telah dilakukan',
        ]);

        $this->alert('success', 'Proses berhasil!', [
            'position' => 'center',
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagal!', [
            'position' => 'center',
        ]);
    }
};

?>

<x-admin-layout>
    <x-slot name="title">Detail Transaksi</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('transactions.index') }}">Transaksi</a>
        </li>
        <li class="breadcrumb-item">
            <a href="#">Invoice</a>
        </li>
    </x-slot>
    @include('layouts.fancybox')

    @volt
        <div>

            <div class="card">
                <div class="card-body">

                    <!-- Invoice 1 - Bootstrap Brain Component -->
                    <section class="py-3 py-md-5">
                        <div class="row mb-4">
                            <div class="col-6">
                                <button class="btn btn-primary btn-lg text-uppercase">
                                    {{ __('booking.' . $booking->status) }}
                                </button>

                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-dark btn-lg mb-3 d-print-none"
                                    id="printInvoiceBtn">Download
                                    Invoice</button>
                            </div>
                        </div>

                        <div class="row gy-3 mb-3">
                            <div class="col-6">
                                <h4 class="text-uppercase text-primary m-0">Invoice</h4>
                            </div>
                            <div class="col-6">
                                <h4 class="text-uppercase text-primary text-end m-0">{{ $booking->invoice }}</h4>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <small class="h5 fw-bold">Penyewaan</small>
                            <div class="col-12 col-sm-6 col-md-8">
                                <address>
                                    <div>{{ $user->name }}</div>
                                    <div>{{ $user->email }}</div>
                                    <div>{{ $user->phone }}</div>
                                </address>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-end">
                                <address>
                                    <div>{{ $booking->created_at->format('d m Y h:i:s') }}</div>
                                    <div>
                                        Metode Pembayaran :
                                        {{ __('status.' . $booking->payment_method) }}
                                    </div>
                                    <div>
                                        No. Telp Alternatif :
                                        {{ $booking->alternative_phone ?? '-' }}
                                    </div>

                                </address>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-uppercase">Lapangan</th>
                                                <th scope="col" class="text-uppercase">Hari</th>
                                                <th scope="col" class="text-uppercase">Jam</th>
                                                <th scope="col" class="text-uppercase text-end">Type</th>
                                                <th scope="col" class="text-uppercase text-end">Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            @foreach ($booking->bookingTimes as $time)
                                                <tr>
                                                    <th>{{ $time->field->field_name }}</th>
                                                    <th>{{ Carbon::parse($time->booking_date)->format('d-m-Y') }}</th>
                                                    <td>{{ $time->start_time . ' - ' . $time->end_time }}</td>
                                                    <td class="text-end">
                                                        {{ __('type.' . $time->type) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ formatRupiah($time->price) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <th scope="row" colspan="4" class="text-uppercase text-end">Total</th>
                                                <td class="text-end">
                                                    {{ formatRupiah($totalPrice) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <small class="h5 fw-bold">
                                Pembayaran
                            </small>
                            @foreach ($payment->records as $item)
                                <div class="col-md">
                                    <div class="card text-start mb-3 h-100">
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    Status
                                                </div>
                                                <div class="col-6 text-end">
                                                    {{ __('record.' . $item->status) }}
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
                                                    {{ formatRupiah($item->gross_amount) ?? '-' }}
                                                </div>
                                                <div class="col-6">
                                                    Waktu pembayaran
                                                </div>
                                                <div class="col-6 text-end">
                                                    {{ $item->payment_time ?? '-' }}
                                                </div>
                                                <div class="col-6">
                                                    Jenis pembayaran
                                                </div>
                                                <div class="col-6 text-end">
                                                    {{ $item->payment_type ?? '-' }}
                                                </div>
                                                <div class="col-6">
                                                    Detail pembayaran
                                                </div>
                                                <div class="col-6 text-end">
                                                    {{ $item->payment_detail ?? '-' }}
                                                </div>
                                                <div class="col-6">
                                                    Pesan pembayaran
                                                </div>
                                                <div class="col-6 text-end">
                                                    {{ $item->status_message ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($item->status === 'DRAF')
                                        <a wire:click="cashPayment({{ $item->id }})" class="w-100 btn btn-primary"
                                            href="#" role="button">
                                            Bayar ditempat
                                        </a>
                                    @endif


                                </div>
                            @endforeach
                        </div>

                    </section>
                </div>


                <section class="card-footer {{ $booking->status === 'PROCESS' ?: 'd-none' }}">
                    <div class="row">
                        <div class="col-md">
                            <button wire:click='cancelBooking' class="btn btn-danger w-100">
                                Batalkan
                            </button>
                        </div>
                        <div class="col-md">

                            <button wire:click='confirmBooking' class="btn btn-primary w-100">
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    @endvolt
</x-admin-layout>
