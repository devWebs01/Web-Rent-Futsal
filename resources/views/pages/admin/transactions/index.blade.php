<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name("transactions.index");

state(["search"])->url();
usesPagination(theme: "bootstrap");

$bookings = computed(function () {
    return booking::query()->latest()->get();
});

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Transaksi</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route("home") }}">Dashboard</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route("transactions.index") }}">Transaksi</a>
            </li>
        </x-slot>

        @include("components.partials.datatables")

        @volt
            <div>
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive border rounded p-4">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Pelanggan</th>
                                        <th>Invoice</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->bookings as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->invoice }}</td>
                                            <td>{{ __("booking." . $item->status) }}</td>
                                            <td>{{ formatRupiah($item->total_price) }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route("transactions.show", ["booking" => $item->id]) }}"
                                                        class="btn btn-sm btn-primary">Detail</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
