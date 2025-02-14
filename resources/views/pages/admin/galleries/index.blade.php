<?php

use App\Models\Gallery;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('galleries.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$galleries = computed(function () {
    if ($this->search == null) {
        return gallery::query()->latest()->paginate(10);
    } else {
        return gallery::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(['image', 'alt'], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (gallery $gallery) {
    try {
        $gallery->delete();
        $this->alert('success', 'Data gallery berhasil dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data gallery gagal dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data gallery</x-slot>


        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('galleries.create') }}" class="btn btn-primary">Tambah
                                    Galeri</a>
                            </div>
                            <div class="col">
                                <input wire:gallery.live="search" type="search" class="form-control" name="search"
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
                                        <th>Gambar</th>
                                        <th>Keterangan</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->galleries as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>
                                                <img src="{{ Storage::url($item->image) }}" class="object-fit-cover rounded" width="50" height="50"
                                                    alt="{{ $item->alt }}" />

                                            </td>
                                            <td>{{ $item->alt }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('galleries.edit', ['gallery' => $item->id]) }}"
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

                            {{ $this->galleries->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
