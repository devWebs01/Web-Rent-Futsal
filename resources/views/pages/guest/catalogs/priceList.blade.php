<?php

use App\Models\Schedule;
use App\Models\Cart;
use App\Models\BookingTime;
use function Livewire\Volt\{state, computed, uses, rules};
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Carbon\Carbon;

uses([LivewireAlert::class]);

state(['selectDate'])->url();

state([
    'allSchedule' => fn() => Schedule::all(),
    'today' => fn() => Carbon::now()->format('Y-m-d'),
    'activeTab' => 'student', // Tab default
    'field',

    // booking
    'user_id' => fn() => Auth()->user()->id ?? '',
    'field_id' => fn() => $this->field->id,
    'booking_date' => fn() => $this->selectDate ? $this->selectDate : $this->today,
    'start_time',
    'end_time',
    'price',
]);

rules([
    'user_id' => 'required|exists:users,id',
    'field_id' => 'required|exists:fields,id',
]);

$addToCart = function ($slot) {
    // Periksa apakah user sudah login
    if (!Auth::check()) {
        $this->redirect('/login');
    }

    $this->validate();

    // Periksa apakah slot sudah ada di daftar
    $checkCart = Cart::where('user_id', Auth::id())
        ->where('field_id', $this->field_id)
        ->where('booking_date', $this->selectDate ?? $this->today)
        ->where('start_time', explode(' - ', $slot['time'])[0])
        ->where('type', $slot['type']) // Periksa berdasarkan type juga
        ->exists();

    if ($checkCart) {
        $this->alert('warning', 'Waktu sudah ada didaftar!', [
            'position' => 'center',
            'timer' => '2000',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    } else {
        // Tambahkan slot ke daftar
        Cart::create([
            'user_id' => Auth::id(),
            'field_id' => $this->field_id,
            'booking_date' => $this->selectDate ?? $this->today,
            'start_time' => explode(' - ', $slot['time'])[0],
            'end_time' => explode(' - ', $slot['time'])[1],
            'type' => $slot['type'], // Simpan type ke daftar
            'price' => $slot['cost'],
        ]);

        $this->dispatch('cart-updated');

        $this->alert('success', 'Waktu berhasil ditambahkan ke daftar', [
            'position' => 'center',
            'timer' => '2000',
            'toast' => true,
            'timerProgressBar' => true,
            'text' => '',
        ]);
    }
};

$slots = computed(function () {
    $schedules = $this->allSchedule;
    $selectedDate = Carbon::parse($this->selectDate ?? $this->today); // Tanggal yang dipilih
    $today = Carbon::now(); // Tanggal dan waktu sekarang
    $now = $today->format('H:i'); // Waktu sekarang (jam dan menit)

    $bookedSlots = BookingTime::where('field_id', $this->field_id)
        ->where('booking_date', $selectedDate->format('Y-m-d'))
        ->whereHas('booking', function ($query) {
            $query->where('status', '!=', 'CANCEL');
        })
        ->get(['start_time', 'type'])
        ->toArray();

    // Map slot yang sudah dibooking
    $bookedMap = collect($bookedSlots)->mapWithKeys(function ($slot) {
        return [$slot['start_time'] => $slot['type']];
    });

    $slots = [];

    foreach ($schedules as $schedule) {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $startIndex = array_search($schedule->start_day, $days);
        $endIndex = array_search($schedule->end_day, $days);
        $currentIndex = array_search($selectedDate->format('l'), $days);

        if ($startIndex <= $currentIndex && $currentIndex <= $endIndex) {
            $start = Carbon::createFromTimeString($schedule->start_time);
            $end = Carbon::createFromTimeString($schedule->end_time);

            while ($start < $end) {
                $slotStart = $start->format('H:i');
                $slotEnd = $start->addHour()->format('H:i');

                // Periksa apakah slot ini di masa lalu
                $isPast = $selectedDate->isSameDay($today) && $slotStart < $now;
                $isBooked = isset($bookedMap[$slotStart]); // Tidak perlu memeriksa type, cukup cek jika slot sudah dibooking

                $slots[] = [
                    'time' => "$slotStart - $slotEnd",
                    'cost' => $schedule->cost,
                    'type' => $schedule->type,
                    'isBooked' => $isBooked,
                    'isPast' => $isPast,
                ];
            }
        }
    }

    return [
        'student' => array_filter($slots, fn($slot) => $slot['type'] === 'STUDENT'),
        'general' => array_filter($slots, fn($slot) => $slot['type'] === 'GENERAL'),
        'tournament' => array_filter($slots, fn($slot) => $slot['type'] === 'TOURNAMENT'),
    ];
});


$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

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
    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-5 justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link  @if ($this->activeTab === 'student') active @endif"
                wire:click.prevent="setActiveTab('student')" type="button" role="tab">
                <strong>PELAJAR</strong>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link  @if ($this->activeTab === 'general') active @endif"
                wire:click.prevent="setActiveTab('general')" type="button" role="tab">
                <strong>UMUM</strong>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link  @if ($this->activeTab === 'tournament') active @endif"
                wire:click.prevent="setActiveTab('tournament')" type="button" role="tab">
                <strong>TURNAMEN/KERAMAIAN</strong>
            </button>
        </li>
    </ul>


    <!-- Tabs Content -->
    <div class="tab-content mb-3" id="pills-tabContent" wire:poll.10s>
        <!-- Tab Pelajar -->
        <div class="tab-pane fade @if ($this->activeTab === 'student') show active @endif" id="pills-student"
            role="tabpanel">
            <div class="row gap-3 justify-content-evenly">
                @foreach ($this->slots['student'] as $slot)
                    <div class="col-md-3 col-sm-4 card p-0 border-0">
                        <div class="card-body text-center shadow rounded-4">
                            <h5 class="mt-3 text-primary fw-bold">{{ $slot['time'] }}</h5>
                            <p class="fw-bold">{{ formatRupiah($slot['cost']) }}</p>
                            <a class="d-flex justify-content-center align-items-center gap-2 btn btn-outline-dark mb-3
                                        {{ $slot['isBooked'] || $slot['isPast'] ? 'd-none' : '' }}"
                                wire:click.prevent="addToCart({{ json_encode($slot) }})" role="button">
                                <span wire:loading.class='d-none'>
                                    PILIH
                                </span>
                                <span wire:loading.class.remove='d-none' class="spinner-border spinner-border-sm d-none">
                                </span>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tab Umum -->
        <div class="tab-pane fade @if ($this->activeTab === 'general') show active @endif" id="pills-general"
            role="tabpanel">
            <div class="row gap-3 justify-content-evenly">
                @foreach ($this->slots['general'] as $slot)
                    <div class="col-md-3 col-sm-4 card p-0 border-0">
                        <div class="card-body text-center shadow rounded-4">
                            <h5 class="mt-3 text-primary fw-bold">{{ $slot['time'] }}</h5>
                            <p class="fw-bold">{{ formatRupiah($slot['cost']) }}</p>
                            <a class="d-flex justify-content-center align-items-center gap-2 btn btn-outline-dark mb-3
                                        {{ $slot['isBooked'] || $slot['isPast'] ? 'd-none' : '' }}"
                                wire:click.prevent="addToCart({{ json_encode($slot) }})" role="button">
                                <span wire:loading.class='d-none'>
                                    PILIH
                                </span>
                                <span wire:loading.class.remove='d-none' class="spinner-border spinner-border-sm d-none">
                                </span>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tab Turnamen/Keramaian -->
        <div class="tab-pane fade @if ($this->activeTab === 'tournament') show active @endif" id="pills-tournament"
            role="tabpanel">
            <div class="row gap-3 justify-content-evenly">
                @foreach ($this->slots['tournament'] as $slot)
                    <div class="col-md-3 col-sm-4 card p-0 border-0">
                        <div class="card-body text-center shadow rounded-4">
                            <h5 class="mt-3 text-primary fw-bold">{{ $slot['time'] }}</h5>
                            <p class="fw-bold">{{ formatRupiah($slot['cost']) }}</p>
                            <a class="d-flex justify-content-center align-items-center gap-2 btn btn-outline-dark mb-3
                                        {{ $slot['isBooked'] || $slot['isPast'] ? 'd-none' : '' }}"
                                wire:click.prevent="addToCart({{ json_encode($slot) }})" role="button">
                                <span wire:loading.class='d-none'>
                                    PILIH
                                </span>
                                <span wire:loading.class.remove='d-none' class="spinner-border spinner-border-sm d-none">
                                </span>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endvolt