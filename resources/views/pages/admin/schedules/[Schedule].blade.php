<?php

use App\Models\Schedule;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('schedules.edit');

state([
    'start_day' => fn() => $this->schedule->start_day,
    'end_day' => fn() => $this->schedule->end_day,
    'start_time' => fn() => Carbon\carbon::parse($this->schedule->start_time)->format('H:i'),
    'end_time' => fn() => Carbon\carbon::parse($this->schedule->end_time)->format('H:i'),
    'type' => fn() => $this->schedule->type,
    'cost' => fn() => $this->schedule->cost,
    'schedule',
]);

rules([
    'start_day' => 'required|string',
    'end_day' => 'required|string',
    'start_time' => 'required|date_format:H:i|in:' . implode(',', array_map(fn($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00', range(6, 23))),
    'end_time' => 'required|date_format:H:i|after:start_time|in:' . implode(',', array_map(fn($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00', range(6, 23))),
    'type' => 'required|in:GENERAL,STUDENT,TOURNAMENT',
    'cost' => 'required|numeric|min:0',
]);

$edit = function () {
    $schedule = $this->schedule;

    $validateData = $this->validate();

    $schedule->update($validateData);

    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('schedules.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Edit Jadwal</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('schedules.index') }}">Jadwal</a>
        </li>
        <li class="breadcrumb-item">
            <a href="#">Edit Jadwal</a>
        </li>
    </x-slot>

    @volt
    <div>
        <div class="card">
            <div class="card-header">
                <div class="alert alert-primary" role="alert">
                    <strong>Tambah Harga</strong>
                    <p>Pada halaman tambah harga, kamu dapat memasukkan informasi dari schedule baru yang akan disimpan
                        ke
                        sistem.</p>
                </div>
            </div>

            <div class="card-body">
                <form wire:submit="edit">
                    @csrf

                    <div class="row">
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="start_day" class="form-label">Hari Mulai</label>
                                <select class="form-control @error('start_day') is-invalid @enderror"
                                    wire:model="start_day" id="start_day">
                                    <option value="" selected>Pilih Hari Mulai</option>
                                    <option value="Monday">Senin</option>
                                    <option value="Tuesday">Selasa</option>
                                    <option value="Wednesday">Rabu</option>
                                    <option value="Thursday">Kamis</option>
                                    <option value="Friday">Jumat</option>
                                    <option value="Saturday">Sabtu</option>
                                    <option value="Sunday">Minggu</option>
                                </select>
                                @error('start_day')
                                    <small id="start_dayId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="end_day" class="form-label">Hari Selesai</label>
                                <select class="form-control @error('end_day') is-invalid @enderror" wire:model="end_day"
                                    id="end_day">
                                    <option value="" selected>Pilih Hari Selesai</option>
                                    <option value="Monday">Senin</option>
                                    <option value="Tuesday">Selasa</option>
                                    <option value="Wednesday">Rabu</option>
                                    <option value="Thursday">Kamis</option>
                                    <option value="Friday">Jumat</option>
                                    <option value="Saturday">Sabtu</option>
                                    <option value="Sunday">Minggu</option>
                                </select>
                                @error('end_day')
                                    <small id="end_dayId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Waktu Mulai</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    wire:model="start_time" id="start_time" step="3600" list="start-time-options"
                                    placeholder="Pilih waktu mulai" aria-describedby="startTimeError"
                                    value="{{ $start_time }}" />
                                <datalist id="start-time-options">
                                    @foreach (range(6, 23) as $hour)
                                        <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00"></option>
                                    @endforeach
                                </datalist>
                                @error('start_time')
                                    <small id="startTimeError" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    wire:model="end_time" id="end_time" step="3600" list="end-time-options"
                                    placeholder="Pilih waktu selesai" aria-describedby="endTimeError"
                                    value="{{ $end_time }}" />
                                <datalist id="end-time-options">
                                    @foreach (range(6, 23) as $hour)
                                        <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00"></option>
                                    @endforeach
                                </datalist>
                                @error('end_time')
                                    <small id="endTimeError" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe</label>
                                <select class="form-control @error('type') is-invalid @enderror" wire:model="type"
                                    id="type">
                                    <option value="" selected>Pilih Tipe</option>
                                    <option value="GENERAL">Umum</option>
                                    <option value="STUDENT">Pelajar</option>
                                    <option value="TOURNAMENT">Turnamen</option>
                                </select>
                                @error('type')
                                    <small id="typeId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="cost" class="form-label">Harga</label>
                                <input type="number" class="form-control @error('cost') is-invalid @enderror"
                                    wire:model="cost" id="cost" placeholder="Masukkan Harga" />
                                @error('cost')
                                    <small id="costId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                        <div class="col-md align-self-center text-end">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endvolt
</x-admin-layout>