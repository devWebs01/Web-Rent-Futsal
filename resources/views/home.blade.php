<?php

use App\Models\Booking;
use App\Models\BookingTime;
use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, computed};

state([
    'totalBookings' => Booking::count(),
    'totalUnpaidBookings' => Booking::where('status', 'UNPAID')->count(),
    'totalCompletedBookings' => BookingTime::where('status', 'STOP')->count(),
    'totalConfirmedPayments' => PaymentRecord::where('status', 'PAID')->sum('gross_amount'),
    'totalPendingPayments' => PaymentRecord::where('status', 'DRAF')->sum('gross_amount'),
]);

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    </x-slot>


    @volt
    <div>

        <div class="row mb-3">
            <!-- Booking -->
            <div class="col-md mb-4">
                <div class="card">
                    <div class="card-body row align-items-center">
                        <i class="bx bx-calendar-check fs-1 text-primary col-3"></i>
                        <div class="col">
                            <small class="m-0 p-0 fw-bold">Booking</small>
                            <h4 class="fw-bold m-0 p-0">
                                {{ $totalBookings }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Booking Belum Dibayar -->
            <div class="col-md mb-4">
                <div class="card">
                    <div class="card-body row align-items-center">
                        <i class="bx bx-wallet fs-1 text-warning col-3"></i>
                        <div class="col">
                            <small class="m-0 p-0 fw-bold">Booking Belum Dibayar</small>

                            <h4 class="fw-bold m-0 p-0">
                                {{ $totalUnpaidBookings }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Booking Selesai -->
            <div class="col-md mb-4">
                <div class="card">
                    <div class="card-body row align-items-center">
                        <i class="bx bx-check-circle fs-1 text-success col-3"></i>
                        <div class="col">
                            <small class="m-0 p-0 fw-bold">Booking Selesai</small>

                            <h4 class="fw-bold m-0 p-0">
                                {{ $totalCompletedBookings }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Uang Dikonfirmasi -->
            <div class="col-md mb-4">
                <div class="card">
                    <div class="card-body row align-items-center">
                        <i class="bx bx-money fs-1 text-success col-3"></i>
                        <div class="col">
                            <small class="m-0 p-0 fw-bold">Uang Dikonfirmasi</small>
                            <h4 class="fw-bold m-0 p-0">
                                {{ formatRupiah($totalConfirmedPayments) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Uang Pending -->
            <div class="col-md mb-4">
                <div class="card">
                    <div class="card-body row align-items-center">
                        <i class="bx bx-hourglass fs-1 text-warning col-3"></i>
                        <div class="col">
                            <small class="m-0 p-0 fw-bold">Uang Pending</small>
                            <h4 class="fw-bold m-0 p-0">
                                {{ formatRupiah($totalPendingPayments) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card mt-4">
            {{-- @include('monitoring') --}}
        </div>
    </div>
    @endvolt
</x-admin-layout>
