<?php

use App\Models\User;
use App\Models\Booking;
use App\Models\BookingTime;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, computed, uses, usesPagination};
use Carbon\Carbon;

uses([LivewireAlert::class]);
usesPagination();

state(['id']);

$isOvertime = function ($bookingTime) {
    $endTime = Carbon::parse($bookingTime->booking_date . ' ' . $bookingTime->end_time);
    return Carbon::now()->greaterThan($endTime);
};

$getRemainingTime = function ($bookingTime) {
    if ($this->isOvertime($bookingTime)) {
        return null;
    }

    $startTime = Carbon::parse($bookingTime->booking_date . ' ' . $bookingTime->start_time);
    $now = Carbon::now();

    if ($now->diffInMinutes($startTime, false) > 30) {
        return 'Akan datang';
    }

    return $startTime->diff($now);
};

$markComplete = function ($id) {
    $bookingTime = BookingTime::findOrFail($id);
    // Update status BookingTime menjadi 'STOP'
    $bookingTime->update(['status' => 'STOP']);

    // Periksa apakah semua BookingTime dalam Booking sudah selesai atau melewati waktu
    $booking = $bookingTime->booking;

    $allTimesStoppedOrOvertime = $booking->bookingTimes->every(function ($time) {
        $endTime = Carbon::parse($time->booking_date . ' ' . $time->end_time);
        return $time->status === 'STOP' || Carbon::now()->greaterThan($endTime);
    });

    // Jika semua BookingTime selesai atau overtime, update status Booking menjadi 'COMPLETE'
    if ($allTimesStoppedOrOvertime) {
        $booking->update(['status' => 'COMPLETE']);
    }

    $this->alert('success', 'Proses berhasil!', [
        'position' => 'center',
        'timer' => 5000,
        'toast' => true,
    ]);
};

$markCancel = function ($id) {
    $bookingTime = BookingTime::findOrFail($id);

    // Update status BookingTime menjadi 'CANCEL'
    $bookingTime->update(['status' => 'CANCEL']);

    // Periksa apakah semua BookingTime dalam Booking sudah dibatalkan atau melewati waktu saat ini
    $booking = $bookingTime->booking;

    $allTimesCanceledOrOvertime = $booking->bookingTimes->every(function ($time) {
        $endTime = Carbon::parse($time->booking_date . ' ' . $time->end_time);
        return $time->status === 'CANCEL' || Carbon::now()->greaterThan($endTime);
    });

    // Jika semua BookingTime dibatalkan atau overtime, update status Booking menjadi 'CANCEL'
    if ($allTimesCanceledOrOvertime) {
        $booking->update(['status' => 'CANCEL']);
    }

    $this->alert('success', 'Booking berhasil dibatalkan!', [
        'position' => 'center',
        'timer' => 5000,
        'toast' => true,
    ]);
};

$monitoring_bookings = computed(function () {
    $bookings = BookingTime::with(['field', 'booking.user'])
        ->whereDate('booking_date', Carbon::today()) // Filter untuk hanya mengambil data hari ini
        ->get() // Ambil semua data
        ->sortBy(function ($item) {
            $now = Carbon::now();
            $startTime = Carbon::parse($item->booking_date . ' ' . $item->start_time);
            $endTime = Carbon::parse($item->booking_date . ' ' . $item->end_time);

            if ($now->between($startTime, $endTime)) {
                // Waktu berjalan saat ini
                return 1;
            } elseif ($now->diffInHours($startTime, false) <= 2 && $now->isBefore($startTime)) {
                // Waktu akan datang dalam 2 jam
                return 0;
            } elseif ($now->isAfter($endTime)) {
                // Waktu telah berlalu
                return 2;
            } else {
                // Waktu akan datang di luar 2 jam
                return 3;
            }
        })
        ->values(); // Reset indeks array setelah pengurutan

    // Paginasi manual
    $perPage = 10;
    $currentPage = request()->input('page', 1);
    $pagedData = $bookings->slice(($currentPage - 1) * $perPage, $perPage)->all();

    return new \Illuminate\Pagination\LengthAwarePaginator($pagedData, $bookings->count(), $perPage, $currentPage, ['path' => request()->url(), 'query' => request()->query()]);
});

?>

@volt
    <div>
        <div class="card-body">
            <div class="table-responsive rounded-3">
                <table class="table table-bordered table-striped text-center text-nowrap">
                    <thead>
                        <tr>
                            <th>Lapangan</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Sisa Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody wire:poll.3600s>
                        @foreach ($this->monitoring_bookings() as $item)
                            <tr>
                                <td>{{ $item->field->field_name }}</td>
                                <td>
                                    {{ Carbon::parse($item->booking_date)->format('d M Y') }} /
                                    {{ $item->start_time }} - {{ $item->end_time }}
                                </td>
                                <td>
                                    @if ($item->booking->status === 'CANCEL')
                                        Batal
                                    @elseif($item->booking->status === 'PROCESS')
                                        Menunggu Konfimasi Admin
                                    @else
                                        {{ __('status.' . $item->status) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === 'STOP')
                                        <span class="badge bg-success py-2 rounded">Selesai</span>
                                    @elseif ($item->status === 'CANCEL')
                                        <span class="badge bg-danger py-2 rounded">Batal</span>
                                    @elseif ($this->isOvertime($item))
                                        <span class="badge bg-danger py-2 rounded">
                                            <i class="bx bxs-bell fs-5 bx-tada"></i> Overtime!
                                        </span>
                                    @else
                                        @if (is_string($this->getRemainingTime($item)))
                                            <span class="badge bg-secondary py-2 rounded">
                                                {{ $this->getRemainingTime($item) }}
                                            </span>
                                        @else
                                            <span wire:poll.visible.1s class="badge bg-primary py-2 rounded">
                                                {{ $this->getRemainingTime($item)->format('%h jam, %i menit, %s detik') }}
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('transactions.show', ['booking' => $item->booking->id]) }}"
                                            class="btn btn-sm btn-primary">Detail</a>

                                        @if ($item->status === 'WAITING')
                                            <button wire:click="markCancel({{ $item->id }})"
                                                class="btn btn-danger btn-sm">Batalkan</button>
                                        @elseif ($item->booking->status === 'CONFIRM')
                                            <button wire:click="markComplete({{ $item->id }})"
                                                class="btn btn-success btn-sm">Selesai</button>
                                        @endif
                                    </div>
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
@endvolt
