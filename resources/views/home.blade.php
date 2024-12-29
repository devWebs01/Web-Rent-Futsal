<?php

use App\Models\User;
use App\Models\BookingTime;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, computed, uses};
use Carbon\Carbon;

uses([LivewireAlert::class]);

state(['id']);

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
        ->get();
});

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    @volt
        <div class="card mt-4">
            <h2 class="card-header mb-4">Monitoring Pemesanan</h2>
            <div class="card-body ">
                <div class="table-responsive rounded">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>Lapangan</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Sisa Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody wire:poll.1s>
                            @foreach ($this->monitoring_bookings() as $item)
                                @php
                                    $endTime = Carbon::parse($item->booking_date . ' ' . $item->end_time);
                                    $now = Carbon::now();
                                    $overtime = $now->greaterThan($endTime);
                                    $remainingTime = !$overtime ? $endTime->diff($now) : null;
                                @endphp
                                <tr>
                                    <td>{{ $item->field->field_name }}</td>
                                    <td>{{ $item->start_time }} - {{ $item->end_time }}</td>
                                    <td>{{ __('status.' . $item->status) }}</td>
                                    <td>
                                        <span class="badge bg-primary py-2 rounded">
                                            @if ($item->status === 'STOP')
                                                Selesai
                                            @elseif ($overtime)
                                                Overtime!
                                            @else
                                                {{ $remainingTime->format('%h jam, %i menit, %s detik') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="markComplete({{ $item->id }})"
                                            class="btn btn-primary btn-sm {{ $item->status !== 'STOP' ?: 'd-none' }}">
                                            Tandai Selesai
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
