<?php

use App\Models\Booking;
use App\Models\BookingTime;
use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, computed};

$totalBookings = computed(function () {
    return Booking::count();
});

$totalUnpaidBookings = computed(function () {
    return Booking::where('status', 'UNPAID')->count();
});

$totalCompletedBookings = computed(function () {
    return BookingTime::where('status', 'STOP')->count();
});

$totalConfirmedPayments = computed(function () {
    return PaymentRecord::where('status', 'CONFIRM')->sum('amount');
});

$totalPendingPayments = computed(function () {
    return PaymentRecord::where('status', 'WAITING')->sum('amount');
});

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    @volt
        <div>

            <div class="card mt-4">

                <div class="card-body">
                    <div class="row">
                        <!-- Booking -->
                        <div class="col-md">
                            <div class="card">
                                <div class="card-body py-2 row align-items-center">
                                    <i class="bx bx-calendar-check fs-1 text-primary col-3"></i>
                                    <div class="col">
                                        <small class="m-0 p-0 fw-bold">Booking</small>
                                        <h4 class="fw-bold m-0 p-0">{{ $this->totalBookings() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Booking Belum Dibayar -->
                        <div class="col-md">
                            <div class="card">
                                <div class="card-body py-2 row align-items-center">
                                    <i class="bx bx-wallet fs-1 text-warning col-3"></i>
                                    <div class="col">
                                        <small class="m-0 p-0 fw-bold">Booking Belum Dibayar</small>

                                        <h4 class="fw-bold m-0 p-0">{{ $this->totalUnpaidBookings() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Booking Selesai -->
                        <div class="col-md">
                            <div class="card">
                                <div class="card-body py-2 row align-items-center">
                                    <i class="bx bx-check-circle fs-1 text-success col-3"></i>
                                    <div class="col">
                                        <small class="m-0 p-0 fw-bold">Booking Selesai</small>

                                        <h4 class="fw-bold m-0 p-0">{{ $this->totalCompletedBookings() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Uang Dikonfirmasi -->
                        <div class="col-md">
                            <div class="card">
                                <div class="card-body py-2 row align-items-center">
                                    <i class="bx bx-money fs-1 text-success col-3"></i>
                                    <div class="col">
                                        <small class="m-0 p-0 fw-bold">Uang Dikonfirmasi</small>
                                        <h4 class="fw-bold m-0 p-0">Rp
                                            {{ number_format($this->totalConfirmedPayments(), 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Uang Pending -->
                        <div class="col-md">
                            <div class="card">
                                <div class="card-body py-2 row align-items-center">
                                    <i class="bx bx-hourglass fs-1 text-warning col-3"></i>
                                    <div class="col">
                                        <small class="m-0 p-0 fw-bold">Uang Pending</small>
                                        <h4 class="fw-bold m-0 p-0">Rp
                                            {{ number_format($this->totalPendingPayments(), 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card">
                        <div class="d-flex align-items-start row">
                            <div class="col-sm-7">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold text-primary mb-3">Monitoring Lapangan </h5>

                                    <p>
                                        Waktu booking yang akan segera habis, harap segera memperbarui
                                        status menjadi
                                        <strong>Selesai</strong> untuk menghindari masalah.
                                    </p>
                                    <p>
                                        Waktu booking berikutnya akan otomatis diperbarui menjadi Berjalan
                                        setelah status
                                        <strong>booking dikonfirmasi</strong>.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-5 text-center text-sm-left">
                                <div class="card-body pb-0 px-0 px-md-6">
                                    <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="175"
                                        class="scaleX-n1-rtl" alt="View Badge User">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('monitoring')
            </div>
        </div>
    @endvolt
</x-admin-layout>
