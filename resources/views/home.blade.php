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
    return $this->bookings = BookingTime::with('field', 'booking.user')
        // ->whereHas('booking', function ($query) {
        //     $query->where('status', 'CONFIRM'); // Atur status booking yang ingin dimonitor
        // })
        ->paginate(5);
});

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    @volt
        <div>

            <div class="card mt-4">
                <h5 class="card-header fw-bold text-center">
                    Monitoring Pemesanan
                    <br>
                    <div wire:loading wire:target='markComplete' class="d-none spinner-border spinner-border-sm"
                        wire:loading.class.remove="d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </h5>
                <div class="card-body">
                    <div class="table-responsive rounded">
                        <table class="table table-bordered table-striped text-center table-sm">
                            <thead class="fw-bold">
                                <tr>
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
                                        <td>{{ $item->field->field_name }}</td>
                                        <td>{{ Carbon::parse($item->booking_date)->format('d M Y') }}</td>
                                        <td>{{ $item->start_time }} - {{ $item->end_time }}</td>
                                        <td>{{ __('status.' . $item->status) }}</td>
                                        <td>
                                            @if ($item->status === 'STOP')
                                                <button class="badge bg-success py-2 rounded">
                                                    Selesai
                                                </button>
                                            @elseif ($this->isOvertime($item))
                                                <button class="badge bg-danger py-2 rounded">
                                                    <i class='bx bxs-bell fs-5 bx-tada'></i>
                                                    Overtime!
                                                </button>
                                            @else
                                                @php
                                                    $remainingTime = $this->getRemainingTime($item);
                                                @endphp
                                                @if (is_string($remainingTime))
                                                    <button class="badge bg-secondary py-2 rounded">
                                                        {{ $remainingTime }}
                                                    </button>
                                                @else
                                                    <button wire:poll.visible.1s class="badge bg-primary py-2 rounded">
                                                        {{ $remainingTime->format('%h jam, %i menit, %s detik') }}
                                                    </button>
                                                @endif
                                            @endif
                                        </td>

                                        <td>
                                            <button wire:click="markComplete({{ $item->id }})"
                                                class="btn btn-primary btn-sm {{ $item->status === 'START' ?: 'd-none' }}">
                                                Tandai Selesai
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>


                            {{ $this->monitoring_bookings()->links() }}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
