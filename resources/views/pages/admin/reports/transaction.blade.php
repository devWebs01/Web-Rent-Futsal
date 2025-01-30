<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('reports.index');

state([
    'bookings' => booking::latest()->get(),
]);

?>

<x-admin-layout>
    <x-slot name="title">Laporan Transaksi</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('reports.index') }}">Laporan Transaksi</a>
        </li>
    </x-slot>

    @include('layouts.print')

    @volt
    <div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Pelanggan</th>
                                <th>Invoice</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $no => $item)
                                <tr>
                                    <td>{{ ++$no }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->invoice }}</td>
                                    <td>{{ formatRupiah($item->total_price) }}</td>
                                    <td>{{ __('status.' . $item->status) }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    @endvolt


</x-admin-layout>