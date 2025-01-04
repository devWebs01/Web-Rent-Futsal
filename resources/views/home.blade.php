<?php

use App\Models\User;
use App\Models\BookingTime;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, computed, uses, usesPagination};
use Carbon\Carbon;

uses([LivewireAlert::class]);
usesPagination();

state(['id']);

$isOvertime = function ($booking) {
    $endTime = Carbon::parse($booking->booking_date . ' ' . $booking->end_time);
    return Carbon::now()->greaterThan($endTime);
};

$getRemainingTime = function ($booking) {
    if ($this->isOvertime($booking)) {
        return null;
    }

    $startTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
    $now = Carbon::now();

    // Jika waktu sekarang lebih dari 30 menit sebelum waktu mulai
    if ($now->diffInMinutes($startTime, false) > 30) {
        return 'Akan datang';
    }

    // Jika sudah dalam 30 menit menuju waktu mulai
    return $startTime->diff($now);
};

$markComplete = function ($id) {
    try {
        // Tandai booking sebagai selesai
        $bookingTime = BookingTime::findOrFail($id);
        $bookingTime->update(['status' => 'STOP']);
        //code...
        $this->alert('success', 'Proses berhasil!', [
            'position' => 'center',
            'timer' => 5000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagak!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

$monitoring_bookings = computed(function () {
    return BookingTime::with('field', 'booking.user')
        ->latest()
        ->paginate(10);
});

$totalBookings = computed(function () {
    return \App\Models\Booking::count();
});

$totalUnpaidBookings = computed(function () {
    return \App\Models\Booking::where('status', 'UNPAID')->count();
});

$totalCompletedBookings = computed(function () {
    return \App\Models\BookingTime::where('status', 'STOP')->count();
});

$totalConfirmedPayments = computed(function () {
    return \App\Models\PaymentRecord::where('status', 'CONFIRM')->sum('amount');
});

$totalPendingPayments = computed(function () {
    return \App\Models\PaymentRecord::where('status', 'WAITING')->sum('amount');
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

                <div class="card-body">
                   
                    <div class="table-responsive rounded-3">
                        <table class="table table-bordered table-striped text-center table-sm small">
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Lapangan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Sisa Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->monitoring_bookings() as $item)
                                    <tr>
                                        <td>{{ $item->booking->user->name }}</td>
                                        <td>{{ $item->field->field_name }}</td>
                                        <td>{{ Carbon::parse($item->booking_date)->format('d M Y') }}</td>
                                        <td>{{ $item->start_time }} - {{ $item->end_time }}</td>
                                        <td>
                                            @if ($item->booking->status === 'CANCEL')
                                                Batal
                                            @else
                                                {{ __('status.' . $item->status) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status === 'STOP')
                                                <button class="badge bg-success py-2 rounded">
                                                    Selesai
                                                </button>
                                            @elseif ($item->booking->status === 'CANCEL')
                                                <button class="badge bg-danger py-2 rounded">
                                                    Batal
                                                </button>
                                            @elseif ($this->isOvertime($item))
                                                <button class="badge bg-danger py-2 rounded">
                                                    <i class='bx bxs-bell fs-5 bx-tada'></i>
                                                    Overtime!
                                                </button>
                                            @else
                                                @if (is_string($this->getRemainingTime($item)))
                                                    <button class="badge bg-secondary py-2 rounded">
                                                        {{ $this->getRemainingTime($item) }}
                                                    </button>
                                                @else
                                                    <button wire:poll.visible.1s class="badge bg-primary py-2 rounded">
                                                        {{ $this->getRemainingTime($item)->format('%h jam, %i menit, %s detik') }}
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status === 'WAITING')
                                                <a href="{{ route('transactions.show', ['booking' => $item->booking->id]) }}"
                                                    class="btn btn-sm btn-warning">Cek</a>
                                            @elseif (
                                                $item->status === 'CONFIRM' &&
                                                    Carbon::now()->between(Carbon::parse($item->booking_date . ' ' . $item->start_time),
                                                        Carbon::parse($item->booking_date . ' ' . $item->end_time)))
                                                <button wire:click="markComplete({{ $item->id }})"
                                                    class="btn btn-primary btn-sm">
                                                    Tandai Selesai
                                                </button>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pt-5">
                            {{ $this->monitoring_bookings()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
