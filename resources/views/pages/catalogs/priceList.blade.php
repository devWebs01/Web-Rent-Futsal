<?php

use App\Models\Price;
use function Livewire\Volt\{state, computed, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Carbon\Carbon;

uses([LivewireAlert::class]);

state(['selectDate'])->url();

state([
    'allPrice' => fn() => Price::all(),
    'today' => fn() => Carbon::now()->format('Y-m-d'),
    'field',
]);

$slots = computed(function () {
    $prices = $this->allPrice;
    $date = Carbon::parse($this->selectDate)->format('l') ?? Carbon::now()->format('l');

    $slots = []; // Deklarasikan array kosong untuk menyimpan slot waktu

    foreach ($prices as $price) {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $startIndex = array_search($price->start_day, $days);
        $endIndex = array_search($price->end_day, $days);
        $currentIndex = array_search($date, $days);

        // Filter slot hanya untuk hari ini
        if ($startIndex <= $currentIndex && $currentIndex <= $endIndex) {
            $start = Carbon::createFromTimeString($price->start_time);
            $end = Carbon::createFromTimeString($price->end_time);

            while ($start < $end) {
                $slotStart = $start->format('H:i');
                $slotEnd = $start->addHour()->format('H:i');
                $slots[] = [
                    'time' => "$slotStart - $slotEnd",
                    'cost' => $price->cost,
                    'type' => $price->type,
                ];
            }
        }
    }

    // Pisahkan slot berdasarkan type
    return [
        'student' => array_filter($slots, fn($slot) => $slot['type'] === 'STUDENT'),
        'general' => array_filter($slots, fn($slot) => $slot['type'] === 'GENERAL'),
        'tournament' => array_filter($slots, fn($slot) => $slot['type'] === 'TOURNAMENT'),
    ];
});

?>

@volt
    <div>
        <div class="row justify-content-center">
            <div class="col-8 mb-3 ">
                <label for="selectDate" class="form-label">Pilih Tanggal</label>
                <input type="date" wire:model.live='selectDate' class="form-control" name="selectDate" id="selectDate"
                    min="{{ $today }}" value="{{ $today }}" placeholder="Please select date" />
            </div>

        </div>
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills mb-5 justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-student-tab" data-bs-toggle="pill" data-bs-target="#pills-student"
                    type="button" role="tab" aria-controls="pills-student" aria-selected="true">
                    <strong>PELAJAR</strong>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-general-tab" data-bs-toggle="pill" data-bs-target="#pills-general"
                    type="button" role="tab" aria-controls="pills-general" aria-selected="false">
                    <strong>UMUM</strong>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-tournament-tab" data-bs-toggle="pill" data-bs-target="#pills-tournament"
                    type="button" role="tab" aria-controls="pills-tournament" aria-selected="false">
                    <strong>TURNAMEN/KERAMAIAN</strong>
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content mb-3" id="pills-tabContent">
            <!-- Tab Pelajar -->
            <div class="tab-pane fade show active" id="pills-student" role="tabpanel" aria-labelledby="pills-student-tab">
                <div class="row gap-3 justify-content-evenly">
                    @foreach ($this->slots['student'] as $slot)
                        <div class="col-md-3 col-sm-4 card p-0 border-0">
                            <div class="card-body text-center shadow rounded-4">
                                <h5 class="mt-3 text-danger">{{ $slot['time'] }}</h5>
                                <p class="fw-bold">
                                    {{ formatRupiah($slot['cost']) }}
                                </p>
                                <a class="d-grid btn btn-outline-dark mb-3" href="#" role="button">
                                    Booking
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab Umum -->
            <div class="tab-pane fade" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
                <div class="row gap-3 justify-content-evenly">
                    @foreach ($this->slots['general'] as $slot)
                        <div class="col-md-3 col-sm-4 card p-0 border-0">
                            <div class="card-body text-center shadow rounded-4">
                                <h5 class="mt-3 text-danger">{{ $slot['time'] }}</h5>
                                <p class="fw-bold">
                                    {{ formatRupiah($slot['cost']) }}
                                </p>
                                <a class="d-grid btn btn-outline-dark mb-3" href="#" role="button">
                                    Booking
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab Turnamen/Keramaian -->
            <div class="tab-pane fade" id="pills-tournament" role="tabpanel" aria-labelledby="pills-tournament-tab">
                <div class="row gap-3 justify-content-evenly">
                    @foreach ($this->slots['tournament'] as $slot)
                        <div class="col-md-3 col-sm-4 card p-0 border-0">
                            <div class="card-body text-center shadow rounded-4">
                                <h5 class="mt-3 text-danger">{{ $slot['time'] }}</h5>
                                <p class="fw-bold">
                                    {{ formatRupiah($slot['cost']) }}
                                </p>
                                <a class="d-grid btn btn-outline-dark mb-3" href="#" role="button">
                                    Booking
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endvolt
