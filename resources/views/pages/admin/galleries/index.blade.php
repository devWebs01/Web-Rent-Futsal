<?php

use App\Models\Gallery;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, uses};

uses([LivewireAlert::class]);

name("galleries.index");

$galleries = computed(function () {
    return gallery::query()->latest()->get();
});

$destroy = function (gallery $gallery) {
    try {
        $gallery->delete();
        $this->alert("success", "Data gallery berhasil dihapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Data gallery gagal dihapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }

    $this->redirectRoute("galleries.index");
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data gallery</x-slot>

        @include("components.partials.datatables")

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route("galleries.create") }}" class="btn btn-primary">Tambah
                            Galeri</a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded p-4">
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
                                                <img src="{{ Storage::url($item->image) }}" class="object-fit-cover rounded"
                                                    width="50" height="50" alt="{{ $item->alt }}" />

                                            </td>
                                            <td>{{ $item->alt }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route("galleries.edit", ["gallery" => $item->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $item->id }})'
                                                        wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                        class="btn btn-sm btn-danger">
                                                        {{ __("Hapus") }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
