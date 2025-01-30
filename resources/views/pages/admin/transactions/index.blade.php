<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('transactions.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$bookings = computed(function () {
    if ($this->search == null) {
        return booking::query()->latest()->paginate(10);
    } else {
        return booking::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(['invoice', 'status', 'total_price', 'payment_method'], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Transaksi</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('transactions.index') }}">Transaksi</a>
            </li>
        </x-slot>

        @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        {{-- <div class="col">
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary">Tambah
                                booking</a>
                        </div> --}}
                        <div class="col">
                            <input wire:model.live="search" type="search" class="form-control" name="search" id="search"
                                aria-describedby="searchId" placeholder="Masukkan kata kunci pencarian" />
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive border rounded">
                        <table class="table table-striped text-center text-nowrap">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Pelanggan</th>
                                    <th>Invoice</th>
                                    <th>Status</th>
                                    <th>Total Harga</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->bookings as $no => $item)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ $item->invoice }}</td>
                                        <td>{{ formatRupiah($item->total_price) }}</td>
                                        <td>{{ __('status.' . $item->status) }}</td>
                                        <td>
                                            <div>
                                                <a href="{{ route('transactions.show', ['booking' => $item->id]) }}"
                                                    class="btn btn-sm btn-primary">Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        {{ $this->bookings->links() }}
                    </div>

                </div>
            </div>
        </div>
        @endvolt

    </div>
</x-admin-layout>