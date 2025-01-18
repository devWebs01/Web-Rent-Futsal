<?php

use App\Models\Field;
use App\Models\Schedule;
use App\Models\Booking;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, computed, uses, usesPagination};

uses([LivewireAlert::class]);
usesPagination();

$fields = computed(function () {
    return Field::with('bookingTimes')->get(); // Mengambil lapangan dengan jadwal yang terkait
});

$types = computed(function () {
    return Schedule::pluck('type')->unique(); // Mengambil jenis lapangan yang unik
});

$schedules = computed(function () {
    return Schedule::orderBy('start_time')->get(); // Mengambil semua jadwal terurut dari yang paling awal
});

$bookings = computed(function () {
    return Booking::get();
});

?>

@volt
<div>
    <div class="card-body mb-4">
        <div class="table-responsive">
            <table class="table table-striped text-center text-nowrap">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Pelanggan</th>
                        <th>Invoice</th>
                        <th>Status</th>
                        <th>Total Harga</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->bookings as $no => $item)
                        <tr>
                            <td>{{ ++$no }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->invoice }}</td>
                            <td>{{ formatRupiah($item->total_price) }}</td>
                            <td>{{ __('status.' . $item->status) }}</td>
                            <td>
                                <div>
                                    <a href="{{ route('transactions.show', ['booking' => $item->id]) }}"
                                        class="btn btn-sm btn-primary">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <br>

    <div class="card-body">
        <!-- Tab Lapangan -->
        <ul class="nav nav-pills nav-justified justify-content-center" id="fieldTabs" role="tablist">
            @foreach ($this->fields() as $field)
                <li class="nav-item" role="presentation">
                    <a class="text-uppercase nav-link @if ($loop->first) active @endif" id="tab-{{ $field->id }}"
                        data-bs-toggle="tab" href="#content-{{ $field->id }}" role="tab"
                        aria-controls="content-{{ $field->id }}" aria-selected="true">

                        {{ ucfirst($field->field_name) }}

                    </a>
                </li>
            @endforeach
        </ul>

        <!-- Tab Content Lapangan -->
        <div class="tab-content px-0" id="fieldTabsContent">
            @foreach ($this->fields() as $field)
                <div class="tab-pane fade @if ($loop->first) show active @endif" id="content-{{ $field->id }}"
                    role="tabpanel" aria-labelledby="tab-{{ $field->id }}">
                    <!-- Sub-Tab Jenis Lapangan -->
                    <ul class="nav nav-pills nav-justified mt-3 justify-content-center" id="typeTabs-{{ $field->id }}"
                        role="tablist">
                        @foreach ($this->types() as $type)
                            <li class="nav-item" role="presentation">
                                <a class="text-uppercase nav-link @if ($loop->first) active @endif"
                                    id="tab-{{ $field->id }}-{{ $type }}" data-bs-toggle="tab"
                                    href="#content-{{ $field->id }}-{{ $type }}" role="tab"
                                    aria-controls="content-{{ $field->id }}-{{ $type }}" aria-selected="true">
                                    {{ __('type.' . ucfirst($type)) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Sub-Tab Content Jenis Lapangan -->
                    <div class="tab-content px-0" id="typeTabsContent-{{ $field->id }}">

                        @foreach ($this->types() as $type)
                            <div class="tab-pane fade @if ($loop->first) show active @endif"
                                id="content-{{ $field->id }}-{{ $type }}" role="tabpanel"
                                aria-labelledby="tab-{{ $field->id }}-{{ $type }}">

                                <div class="table-responsive rounded-3">

                                    <table class="table table-bordered table-striped text-center text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Lapangan</th>
                                                <th>Jadwal</th>
                                                <th>Biaya</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody wire:poll.3600s>
                                            @foreach ($this->schedules() as $schedule)
                                                @if ($schedule->type == $type)
                                                    <tr>
                                                        <td>{{ $field->field_name }}</td>
                                                        <td>
                                                            {{ Carbon::parse($schedule->start_time)->format('H:i') }}
                                                            -
                                                            {{ Carbon::parse($schedule->end_time)->format('H:i') }}
                                                        </td>
                                                        <td>{{ formatRupiah($schedule->cost) }}</td>
                                                        <td>
                                                            @if ($field->bookingTimes->where('start_time', $schedule->start_time)->isNotEmpty())
                                                                <span class="badge bg-danger">Tidak Tersedia</span>
                                                            @else
                                                                <span class="badge bg-success">Tersedia</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($field->bookingTimes->where('start_time', $schedule->start_time)->isEmpty())
                                                                <button class="btn btn-sm btn-primary">Pesan</button>
                                                            @else
                                                                <button class="btn btn-sm btn-secondary" disabled>Pesan</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endvolt
