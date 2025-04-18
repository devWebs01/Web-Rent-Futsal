<?php

use App\Models\Schedule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('schedules.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$schedules = computed(function () {
    if ($this->search == null) {
        return schedule::query()->latest()->paginate(10);
    } else {
        return schedule::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(['start_day', 'end_day', 'start_time', 'end_time', 'type', 'cost'], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (schedule $schedule) {
    try {
        $schedule->delete();
        $this->alert('success', 'Data jadwal berhasil dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data jadwal gagal dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Jadwal</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('schedules.index') }}">Jadwal</a>
            </li>
        </x-slot>

        @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('schedules.create') }}" class="btn btn-primary">Tambah
                                Jadwal</a>
                        </div>
                        <div class="col">
                            <input wire:model.live="search" type="search" class="form-control" name="search" id="search"
                                aria-describedby="searchId" placeholder="Masukkan kata kunci pencarian" />
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive border rounded">
                        <table class="table table-striped text-center text-nowrap">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->schedules as $no => $item)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ __('day.' . $item->start_day) }} - {{ __('day.' . $item->end_day) }}</td>
                                        <td>{{ $item->start_time }} - {{ $item->end_time }}</td>
                                        <td>{{ __('type.' . $item->type) }}</td>
                                        <td>{{ formatRupiah($item->cost) }}</td>
                                        <td>
                                            <div>
                                                <a href="{{ route('schedules.edit', ['schedule' => $item->id]) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <button wire:loading.attr='disabled' wire:click='destroy({{ $item->id }})'
                                                    wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                    class="btn btn-sm btn-danger">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                    <div class="m-3">
                        {{ $this->schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endvolt

    </div>
</x-admin-layout>
