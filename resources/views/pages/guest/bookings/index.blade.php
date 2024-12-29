<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{state, uses};

uses([LivewireAlert::class]);

name('bookings.index');

state(['search'])->url();
state([
    'auth' => fn() => Auth::user(),
    'bookings' => fn() => booking::where('user_id', $this->auth->id)
        ->latest()
        ->get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Data booking</x-slot>
    @include('layouts.datatables')

    @volt
        <div>
            <div class="container">
                <div class="card rounded-4 mb-3">
                    <div class="card-body">
                        <div class="table-responsive border-0 rounded">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Invoice</th>
                                        <th>Status</th>
                                        <th>Total Bayar</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->invoice }}</td>
                                            <td>{{ __('status.' . $item->status) }}</td>
                                            <td>{{ formatRupiah($item->total_price) }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('bookings.show', ['booking' => $item->id]) }}"
                                                        wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                        class="btn btn-sm btn-danger">
                                                        Detail
                                                    </a>
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
        </div>
    @endvolt

</x-guest-layout>
