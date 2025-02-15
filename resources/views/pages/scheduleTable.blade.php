<?php

use Carbon\Carbon;
use App\Models\Field;
use App\Models\Schedule;
use App\Models\BookingTime;
use function Livewire\Volt\{state, mount, computed};

state([
    'slots' => [],
    'selectDate' => null,
    'today' => null,
]);

mount(function () {
    // Set tanggal default
    $this->today = Carbon::now()->format('Y-m-d');
    $this->selectDate = $this->today;
    $this->loadSlots();
});

/**
 * Fungsi untuk memuat slot dari jam paling awal hingga paling akhir.
 */
$loadSlots = computed(function () {
    $selectedDate = Carbon::parse($this->selectDate);
    $today = Carbon::now();
    $now = $today->format('H:i');

    // Ambil semua lapangan
    $fields = Field::all();

    // Ambil semua jadwal
    $allSchedules = Schedule::all();

    // Booking time yang sudah terpesan untuk tanggal terpilih
    // Dikelompokkan per field_id agar mudah dicek nanti
    $bookedSlots = BookingTime::where('booking_date', $selectedDate->format('Y-m-d'))
        ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['CANCEL']))
        ->get(['field_id', 'start_time'])
        ->groupBy('field_id');

    $slots = [];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $currentIndex = array_search($selectedDate->format('l'), $days);

    foreach ($fields as $field) {
        // 1) Filter schedules milik lapangan ini dan yang berlaku di hari terpilih
        $fieldSchedules = $allSchedules->filter(function ($sch) use ($field, $days, $currentIndex) {
            // Jika schedule punya field_id, cek apakah sama dengan ID lapangan
            if (isset($sch->field_id) && $sch->field_id != $field->id) {
                return false;
            }
            // Cek hari
            $startIndex = array_search($sch->start_day, $days);
            $endIndex = array_search($sch->end_day, $days);
            if ($startIndex === false || $endIndex === false) {
                return false;
            }
            return $startIndex <= $currentIndex && $currentIndex <= $endIndex;
        });

        // 2) Cari jam paling awal dan paling akhir di hari ini (jika ada)
        $earliest = $fieldSchedules->min('start_time');
        $latest = $fieldSchedules->max('end_time');

        // Kalau lapangan ini tidak ada schedule sama sekali hari itu, skip
        if (!$earliest || !$latest) {
            $slots[$field->field_name] = [];
            continue;
        }

        // Konversi ke Carbon untuk loop per jam
        $startTime = Carbon::createFromTimeString($earliest);
        $endTime = Carbon::createFromTimeString($latest);

        $slotTimes = [];
        while ($startTime < $endTime) {
            $slotStart = $startTime->format('H:i');
            $nextHour = $startTime->copy()->addHour();
            $slotEnd = $nextHour->format('H:i');

            // Cek apakah jam ini berada dalam salah satu schedule
            // (Misal, kalau jam 09:00 berada dalam schedule 08:00 - 10:00)
            $coveringSchedule = $fieldSchedules->first(function ($sch) use ($slotStart) {
                $schStart = Carbon::createFromTimeString($sch->start_time);
                $schEnd = Carbon::createFromTimeString($sch->end_time);
                return $schStart->format('H:i') <= $slotStart && $slotStart < $schEnd->format('H:i');
            });

            // Jika ada schedule yang men-cover jam ini, ambil cost. Jika tidak, anggap “0” atau “-”
            $cost = $coveringSchedule ? $coveringSchedule->cost : 0;

            // Periksa booking
            $isBooked = isset($bookedSlots[$field->id]) && collect($bookedSlots[$field->id])->contains('start_time', $slotStart);

            // Cek apakah waktu sudah lewat
            $isPast = $selectedDate->isSameDay($today) && $slotStart < $now;

            // Masukkan data slot
            $slotTimes[] = [
                'field_id' => $field->id,
                'time' => "$slotStart - $slotEnd",
                'cost' => $cost,
                'isBooked' => $isBooked,
                'isPast' => $isPast,
            ];

            $startTime->addHour(); // increment 1 jam
        }

        $slots[$field->field_name] = $slotTimes;
    }

    // Simpan hasil ke state
    $this->slots = $slots;
});

$updatedSelectDate = fn() => $this->loadSlots();

?>

@volt
    <div>

        <div class="bg-light py-lg-14 py-12 bg-cover wow fadeInUp" data-wow-delay="0.2s">
            <!-- container -->
            <div class="container-fluid px-3 ">
                <!-- row -->
                <div class="row align-items-center">
                    <div class="col-lg col-12">
                        <div>
                            <div class=" text-center text-md-start mt-5">
                                <!-- heading -->
                                <span class="text-primary">Jadwal Mainmu Hari Ini</span>

                                <h1 class=" display-2 fw-bold  mb-3">Cek Data Realtime!</h1>
                                <!-- lead -->
                                <p class="lead">Temukan waktu yang sempurna untuk bermain hari ini! Dengan informasi
                                    terkini, kamu bisa merencanakan aktivitasmu dengan lebih mudah. Jangan lewatkan momen
                                    seru, cek jadwal dan sesuaikan dengan waktumu sekarang juga! </p>
                            </div>
                            <div>
                                <!-- card -->
                                <div class="bg-white rounded-md-pill shadow rounded-3 mb-4">
                                    <!-- card body -->
                                    <div class="p-md-2 p-4">
                                        <input type="date" min="{{ $today }}" wire:model.live="selectDate"
                                            id="selectDate" class="form-control">
                                    </div>

                                </div>

                                <!-- Tab Lapangan -->
                                <ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">
                                    @foreach ($slots as $fieldName => $slotTimes)
                                        <li class="nav-item w-100" role="presentation">
                                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                id="pills-{{ Str::slug($fieldName) }}-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-{{ Str::slug($fieldName) }}" type="button"
                                                role="tab" aria-controls="pills-{{ Str::slug($fieldName) }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                {{ $fieldName }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg col-12 text-center">
                        <div class="card bg-light mt-5 border-0">
                            <!-- Konten Tiap Tab Lapangan -->
                            <div class="card-body tab-content p-0 rounded" id="pills-tabContent">
                                @foreach ($slots as $fieldName => $slotTimes)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="pills-{{ Str::slug($fieldName) }}" role="tabpanel"
                                        aria-labelledby="pills-{{ Str::slug($fieldName) }}-tab">

                                        <div class="table-responsive">
                                            <table class="table bg-white table-bordered text-center table-sm"
                                                style="width: 100%">
                                                <thead class="bg-primary text-white">
                                                    <tr>
                                                        <th>Waktu</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($slotTimes as $slot)
                                                        @php
                                                            $rowClass = $slot['isBooked']
                                                                ? 'table-danger'
                                                                : 'table-white';
                                                        @endphp
                                                        <tr class="{{ $rowClass }}">
                                                            <td>{{ $slot['time'] }}</td>
                                                            <td>
                                                                @if ($slot['isPast'])
                                                                    <span class="btn btn-sm btn-warning">Sudah Lewat</span>
                                                                @elseif ($slot['isBooked'])
                                                                    <span class="btn btn-sm btn-danger">Booked</span>
                                                                @elseif ($slot['cost'] === 0)
                                                                    <span class="btn btn-sm btn-light">Tidak Ada
                                                                        Jadwal</span>
                                                                @else
                                                                    <span class="btn btn-sm btn-success">Tersedia</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <span class="text-primary">Jadwal Mainmu Hari Ini</span>
            <h2 class="display-5 fw-bold mb-3">Cek Data Realtime!
            </h2>
            <p class="lead">
                Temukan waktu yang sempurna untuk bermain hari ini! Dengan informasi terkini, kamu bisa merencanakan
                aktivitasmu dengan lebih mudah. Jangan lewatkan momen seru, cek jadwal dan sesuaikan dengan waktumu sekarang
                juga!
            </p>
        </div>
        <section class="card">
            <div class="card-header bg-white">
                <label for="selectDate" class="block font-semibold text-lg mb-2">Pilih Tanggal:</label>
                <input type="date" min="{{ $today }}" wire:model.live="selectDate" id="selectDate"
                    class="form-control">
            </div>

            <div class="card-body table-responsive">
                <!-- Tab Lapangan -->
                <ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">
                    @foreach ($slots as $fieldName => $slotTimes)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="pills-{{ Str::slug($fieldName) }}-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-{{ Str::slug($fieldName) }}" type="button" role="tab"
                                aria-controls="pills-{{ Str::slug($fieldName) }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $fieldName }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Konten Tiap Tab Lapangan -->
                <div class="tab-content" id="pills-tabContent">
                    @foreach ($slots as $fieldName => $slotTimes)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="pills-{{ Str::slug($fieldName) }}" role="tabpanel"
                            aria-labelledby="pills-{{ Str::slug($fieldName) }}-tab">

                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($slotTimes as $slot)
                                            @php
                                                $rowClass = $slot['isBooked'] ? 'table-danger' : 'table-success';
                                            @endphp
                                            <tr class="{{ $rowClass }}">
                                                <td>{{ $slot['time'] }}</td>
                                                <td>
                                                    @if ($slot['isPast'])
                                                        <span class="btn btn-warning">Sudah Lewat</span>
                                                    @elseif ($slot['isBooked'])
                                                        <span class="btn btn-danger">Booked</span>
                                                    @elseif ($slot['cost'] === 0)
                                                        <span class="btn btn-light">Tidak Ada Jadwal</span>
                                                    @else
                                                        <span class="btn btn-success">Tersedia</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </section> --}}
    </div>
@endvolt
