<?php

use App\Models\Field;
use App\Models\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name("fields.index");

state(["search"])->url();
usesPagination(theme: "bootstrap");

$fields = computed(function () {
    if ($this->search == null) {
        return field::query()->latest()->paginate(10);
    } else {
        return field::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(["field_name", "description"], "LIKE", "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (field $field) {
    try {
        $images = Image::where("field_id", $field->id)->get();

        if ($images->isNotEmpty()) {
            // Cek apakah koleksi tidak kosong
            foreach ($images as $image) {
                // Hapus file dari penyimpanan
                Storage::delete($image->image_path);

                // Hapus data dari database
                $image->delete();
            }
        }

        $field->delete();

        $this->alert("success", "Data berhasil dihapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Data gagal dihapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }

    $this->redirectRoute("users.index");

};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Lapangan</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item">
                <a href="{{ route("home") }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route("fields.index") }}">Lapangan</a>
            </li>
        </x-slot>

        @include("components.partials.datatables")


        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route("fields.create") }}" class="btn btn-primary">Tambah
                            Lapangan</a>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded p-4">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Lapangan</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->fields as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->field_name }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route("fields.edit", ["field" => $item->id]) }}"
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
