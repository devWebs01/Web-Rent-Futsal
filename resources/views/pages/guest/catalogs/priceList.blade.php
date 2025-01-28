<?php

use App\Models\Schedule;
use App\Models\Cart;
use App\Models\BookingTime;
use function Livewire\Volt\{state, computed, uses, rules, on};
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Carbon\Carbon;

uses([LivewireAlert::class]);

state(['selectDate'])->url();

state([
    'allSchedule' => fn() => Schedule::all(),
    'today' => fn() => Carbon::now()->format('Y-m-d'),
    'activeTab' => 'student',
    'allTournamentSelected' => false,
    'field',

    // booking
    'user_id' => fn() => Auth()->user()->id ?? '',
    'field_id' => fn() => $this->field->id,
    'booking_date' => fn() => $this->selectDate ? $this->selectDate : $this->today,
    'start_time',
    'end_time',
    'price',
]);

on([
    'allTournamentSelectedFalse' => function () {
        // Reset allTournamentSelected ke false ketika cart diperbarui
        $this->allTournamentSelected = false;
    },
]);

rules([
    'user_id' => 'required|exists:users,id',
    'field_id' => 'required|exists:fields,id',
]);

$selectAllTournamentSlots = function () {
    // Periksa apakah user sudah login
    if (!Auth::check()) {
        $this->redirect('/login');
    }

    // Validasi data
    $this->validate();

    // Ambil semua waktu turnamen
    $tournamentSlots = $this->slots['tournament'];

    // Periksa apakah waktu saat ini sudah melewati waktu waktu yang paling awal
    $earliestSlot = collect($tournamentSlots)->first();
    $latestSlot = collect($tournamentSlots)->last();
    $today = Carbon::now();
    $selectedDate = Carbon::parse($this->selectDate ?? $this->today);
    $now = $today->format('H:i');

    if ($selectedDate->isSameDay($today) && $earliestSlot['time'] < $now) {
        $this->alert('warning', 'Anda tidak dapat memilih waktu waktu yang sudah terlewati!', [
            'position' => 'center',
            'timer' => '3000',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
        return;
    }

    // Periksa apakah ada slot lain yang sudah dipesan untuk lapangan yang sama pada tanggal yang sama
    $existingBookings = BookingTime::where('field_id', $this->field_id)
        ->where('booking_date', $selectedDate->format('Y-m-d'))
        ->where('status', '!=', 'CANCEL') // Abaikan slot yang sudah dibatalkan
        ->exists();

    if ($existingBookings) {
        $this->alert('warning', 'Anda tidak dapat memilih slot turnamen karena sudah ada slot yang dibooking untuk lapangan ini pada tanggal yang sama!', [
            'position' => 'center',
            'timer' => '5000',
            'width' => '500px', // Atur lebar sesuai kebutuhan
            'toast' => true,
            'timerProgressBar' => true,
        ]);
        return;
    }

    $checkCart = Cart::where('user_id', Auth::id())
        ->where('field_id', $this->field_id)
        ->where('booking_date', $this->selectDate ?? $this->today)
        ->exists();

    if ($checkCart) {
        $this->alert('warning', 'Anda tidak dapat memilih waktu turnamen karena waktu waktu sudah ada di keranjang!', [
            'position' => 'center',
            'timer' => '5000',
            'width' => '500px', // Atur lebar sesuai kebutuhan
            'toast' => true,
            'timerProgressBar' => true,
        ]);
        return;
    }

    // Tambahkan waktu ke daftar
    Cart::create([
        'user_id' => Auth::id(),
        'field_id' => $this->field_id,
        'booking_date' => $this->selectDate ?? $this->today,
        'start_time' => explode(' - ', $earliestSlot['time'])[0],
        'end_time' => explode(' - ', $latestSlot['time'])[1],
        'type' => 'TOURNAMENT',
        'price' => $earliestSlot['cost'], // Menggunakan harga dari waktu paling awal
    ]);

    $this->allTournamentSelected = true; // Tandai bahwa semua waktu turnamen sudah dipilih

    $this->dispatch('cart-updated');

    $this->alert('success', 'Slot waktu turnamen berhasil ditambahkan ke keranjang', [
        'position' => 'center',
        'timer' => '3000',
        'toast' => true,
        'timerProgressBar' => true,
    ]);
};

$addToCart = function ($slot) {
    // Periksa apakah user sudah login
    if (!Auth::check()) {
        $this->redirect('/login');
    }

    // Validasi data
    $this->validate();

    // Cek apakah semua waktu turnamen sudah dipilih
    if ($this->allTournamentSelected) {
        $this->alert('warning', 'Anda tidak dapat memesan waktu lain setelah memilih semua waktu turnamen!', [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
        return;
    }

    // Periksa apakah ada waktu lain yang sudah dipesan untuk lapangan yang sama
    $existingBookings = Cart::where('user_id', Auth::id())
        ->where('field_id', $this->field_id)
        ->where('booking_date', $this->selectDate ?? $this->today)
        ->where('start_time', explode(' - ', $slot['time'])[0])
        ->exists();

    if ($existingBookings) {
        $this->alert('warning', 'Anda tidak dapat memesan waktu ini karena waktu sudah ada di keranjang!', [
            'position' => 'center',
            'width' => '500px', // Atur lebar sesuai kebutuhan
            'timer' => '5000',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
        return;
    }

    // Periksa apakah waktu sudah ada di daftar
    $checkCart = Cart::where('user_id', Auth::id())
        ->where('field_id', $this->field_id)
        ->where('booking_date', $this->selectDate ?? $this->today)
        ->where('start_time', explode(' - ', $slot['time'])[0])
        ->exists();

    if ($checkCart) {
        $this->alert('warning', 'Waktu sudah ada di keranjang!', [
            'position' => 'center',
            'timer' => '3000',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    } else {
        // Tambahkan waktu ke daftar
        Cart::create([
            'user_id' => Auth::id(),
            'field_id' => $this->field_id,
            'booking_date' => $this->selectDate ?? $this->today,
            'start_time' => explode(' - ', $slot['time'])[0],
            'end_time' => explode(' - ', $slot['time'])[1],
            'type' => $slot['type'],
            'price' => $slot['cost'],
        ]);

        $this->dispatch('cart-updated');

        $this->alert('success', 'Waktu berhasil ditambahkan ke daftar', [
            'position' => 'center',
            'timer' => '3000',
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

    // Map waktu yang sudah dibooking
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

                // Periksa apakah waktu ini di masa lalu
                $isPast = $selectedDate->isSameDay($today) && $slotStart < $now;
                $isBooked = isset($bookedMap[$slotStart]); // Tidak perlu memeriksa type, cukup cek jika waktu sudah dibooking

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
    <div class="tab-content mb-3" id="pills-tabContent" wire:poll.30s>
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

            <div class="text-center">
                <button class="btn btn-primary btn-lg mb-3" wire:click.prevent="selectAllTournamentSlots">Pilih Semua
                    waktu</button>
            </div>

            <div class="row gap-3 justify-content-evenly">
                @foreach ($this->slots['tournament'] as $slot)
                    <div class="col-md-3 col-sm-4 card p-0 border-0">
                        <div class="card-body text-center shadow rounded-4">
                            <h5 class="mt-3 text-primary fw-bold">{{ $slot['time'] }}</h5>
                            <p class="fw-bold">{{ formatRupiah($slot['cost']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endvolt
