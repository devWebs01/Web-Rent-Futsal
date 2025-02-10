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
    'bookings' => fn() => booking::where('user_id', $this->auth->id)->latest()->get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Data booking</x-slot>
    @include('layouts.datatables')

    @volt
        <div>
            <div class="container">
                <div class="row justify-content-between align-items-center mb-3">
                    <div class="col-md">
                        <h3 class="mb-3">Riwayat Transaksi Anda</h3>
                        <p class="text-muted">
                            Cek status booking terbaru Anda dan pastikan semua transaksi berjalan lancar.
                            Klik <strong>Detail</strong> untuk melihat informasi lebih lanjut.
                        </p>
                    </div>
                    <div class="col-md text-end d-none d-lg-block">
                        <img src="https://cdn.dribbble.com/userupload/11360455/file/original-5bd8c59dc9d53a039fa05f12a0496157.png"
                            class="img rounded" width="400" height="250" style="object-fit: cover" alt="image" />
                    </div>

                </div>

            </div>
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
                                            <td>
                                                <span class="btn btn-light">
                                                    {{ __('booking.' . $item->status) }}
                                                </span>

                                            </td>
                                            <td>{{ formatRupiah($item->total_price) }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('bookings.show', ['booking' => $item->id]) }}"
                                                        wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                        class="btn btn-primary">
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
