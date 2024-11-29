<?php

use App\Models\Price;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('prices.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$prices = computed(function () {
    if ($this->search == null) {
        return price::query()->latest()->paginate(10);
    } else {
        return price::query()
            ->where(function ($query) {
                // isi
                $query->whereAny([' '], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (price $price) {
    try {
        $price->delete();
        $this->alert('success', 'Data price berhasil dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data price gagal dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data price</x-slot>


        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('prices.create') }}" class="btn btn-primary">Tambah
                                    price</a>
                            </div>
                            <div class="col">
                                <input wire:model.live="search" type="search" class="form-control" name="search"
                                    id="search" aria-describedby="searchId"
                                    placeholder="Masukkan kata kunci pencarian" />
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
                                        <th>Type</th>
                                        <th>Harga</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->prices as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ __('day.' . $item->start_day) }} - {{ __('day.' . $item->end_day) }}</td>
                                            <td>{{ $item->start_time }} - {{ $item->end_time }}</td>
                                            <td>{{ __('type.' . $item->type) }}</td>
                                            <td>{{ formatRupiah($item->cost) }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('prices.edit', ['price' => $item->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $item->id }})'
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
                            {{ $this->prices->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
