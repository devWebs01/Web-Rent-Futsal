<?php

use App\Models\Booking;
use App\Models\PaymentRecord;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;
use Carbon\Carbon;

uses([LivewireAlert::class]);

name('transactions.edit');

state([
    'user' => fn() => $this->booking->user,
    'invoice' => fn() => $this->booking->invoice,
    'status' => fn() => $this->booking->status,
    'totalPrice' => fn() => $this->booking->times->sum('price'),
    'payment' => fn() => $this->booking->payment,
    'booking',
    'id',
]);

$confirmReceipt = function ($id) {
    try {
        // Cari data berdasarkan ID
        $receipt = PaymentRecord::findOrFail($id);

        // Update status
        $receipt->update([
            'status' => 'CONFIRM',
        ]);

        $this->alert('success', 'Pembayaran diterima!', [
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
    @include('layouts.fancybox')

    @volt
        <div>
            <x-slot name="title">Booking {{ $invoice }}</x-slot>

            <div class="card">
                <div class="card-header bg-light  justify-content-between align-items-center">
                    <div class="row">
                        <div class="col">
                            <h5 class="mb-0 fw-bold">{{ $invoice }}</h5>
                            <p class="text-muted">{{ $booking->created_at->format('j F, Y \d\i g:i A') }}
                            </p>
                        </div>
                        <div class="col text-end">
                            <span class="badge bg-secondary py-2 rounded">{{ __('status.' . $status) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Order Item -->
                    <div class="my-4">
                        <h5 class="mb-3 fw-bold">Pemesanan</h6>
                            <p>
                                List lapangan dan waktu yang dipilih
                            </p>
                            @foreach ($booking->times as $time)
                                <div class="row mb-3">

                                    @if ($time->field->images->count() < 0)
                                        <img src="https://images.pexels.com/photos/29388472/pexels-photo-29388472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                            class="img-fluid col-2" style="object-fit: cover;">
                                    @else
                                        <img src="{{ Storage::url($time->field->images->first()->image_path) }}"
                                            class="img-fluid col-2" style="object-fit: cover;">
                                    @endif

                                    <div class="col-10">
                                        <div class="row mb-1 fw-bold">
                                            <div class="col-6 fs-5">
                                                {{ $time->field->field_name }}
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ formatRupiah($time->price) }}
                                            </div>
                                        </div>
                                        <p class="mb-1 text-muted">
                                            {{ __('type.' . $time->type) }}
                                        </p>
                                        <p class="mb-1 text-muted">
                                            {{ Carbon::parse($time->booking_date)->format('j F Y') }}
                                            <br>
                                            {{ $time->start_time . ' - ' . $time->end_time }}

                                        </p>
                                    </div>
                                </div>
                            @endforeach
                    </div>

                    <div class="row fw-bold mb-3 text-end">
                        <div class="col-10">Total</div>
                        <div class="col-2">
                            {{ formatRupiah($totalPrice) }}
                        </div>
                    </div>

                    <hr>

                    <!-- Order Summary -->
                    <div class="mb-4 table-responsive">
                        <h5 class="fw-bold mb-3">Pembayaran</h5>
                        <p> Pelanggan
                            <span class="text-primary">{{ $booking->user->name }}</span>
                            memilih menggunakan metode pembayaran
                            <span class="text-primary">
                                {{ __('status.' . $booking->payment_method) }}
                            </span>
                        </p>
                        <table class="table text-center">
                            <tbody>
                                <tr class="fw-bold">
                                    <td>
                                        No.
                                    </td>
                                    <td>
                                        Dibayar
                                    </td>
                                    <td>
                                        Status
                                    </td>
                                    <td>
                                        Bukti Pembayaran
                                    </td>
                                    <td>
                                        #
                                    </td>
                                </tr>
                                @foreach ($payment->records as $no => $record)
                                    <tr>
                                        <td>
                                            {{ ++$no }}
                                        </td>
                                        <td>
                                            {{ formatRupiah($record->amount) }}
                                        </td>
                                        <td>
                                            {{ __('status.' . $record->status) }}
                                        </td>
                                        <td>
                                            @if ($record->receipt)
                                                <a href="{{ Storage::url($record->receipt) }}" data-fancybox
                                                    data-caption="Caption #1">
                                                    <img src="{{ Storage::url($record->receipt) }}" alt="bukti pembayaran"
                                                        class="img object-fit-cover" width="30" height="30">
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($record->receipt && $record->status === 'WAITING')
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-danger">
                                                        Tolak
                                                    </button>

                                                    <button wire:confirm="Yakin ingin mengkonfimasi pembayaran ini?"
                                                        wire:key="{{ $record->id }}"
                                                        wire:click="confirmReceipt({{ $record->id }})" type="button"
                                                        class="btn btn-sm btn-primary">
                                                        Terima
                                                    </button>

                                                </div>
                                            @else
                                                -
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-5">
                            <button class="btn btn-outline-secondary me-2">Send invoice</button>
                            <button class="btn btn-primary">Collect payment</button>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mb-4">
                        <h6 class="mb-3">Timeline</h6>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/40" alt="Alex Jander" class="rounded-circle me-3">
                            <div>
                                <strong>Alex Jander</strong>
                                <p class="text-muted mb-0">First customer and order!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Customer</h6>
                            <p class="mb-1">Alex Jander</p>
                            <p class="mb-1">1 order</p>
                            <p class="text-muted">Customer is tax-exempt</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Contact Information</h6>
                            <p class="mb-1">alexjander@gmail.com</p>
                            <p class="text-muted">+1 628 276 9041</p>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mt-4">
                        <h6 class="mb-3">Shipping Address</h6>
                        <p class="mb-1">Alex Jander</p>
                        <p class="mb-1">1226 University Drive</p>
                        <p class="mb-1">Menlo Park CA 94025</p>
                        <p class="text-muted">United States</p>
                    </div>
                </div>
            </div>

        </div>
    @endvolt
</x-admin-layout>
