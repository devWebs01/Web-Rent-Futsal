<?php

use App\Models\Field;
use App\Models\Image;
use App\Models\Facility;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);
usesFileUploads();

name('fields.edit');

state([
    'field_name' => fn() => $this->field->field_name,
    'description' => fn() => $this->field->description,
    'facilities' => fn() => $this->field->facilities->pluck('facility_name')->toArray(), // Muat facilities sebagai array
    'images' => [],
    'field',
]);

rules([
    'field_name' => 'required|string|min:5',
    'description' => 'required|string|min:5',
    'facilities' => 'required|min:1', // Validasi array
    'facilities.*' => 'required|string|min:5', // Validasi setiap item
    'images' => 'required', // Validasi file gambar
    'images.*' => 'image|max:2048', // Validasi file gambar
]);

$edit = function () {
    $field = $this->field;

    $validateData = $this->validate();

    // Update field
    $field->update([
        'field_name' => $validateData['field_name'],
        'description' => $validateData['description'],
    ]);

    // Pastikan facilities berupa array
    $facilities = is_array($this->facilities) ? $this->facilities : explode(',', $this->facilities);

    // Hapus fasilitas lama dan tambahkan yang baru
    $field->facilities()->delete();
    foreach ($facilities as $facility) {
        $field->facilities()->create([
            'facility_name' => $facility,
        ]);
    }

    // Simpan Images
    foreach ($this->images as $image) {
        $path = $image->store('images'); // Simpan ke folder "fields" di storage
        Image::create([
            'field_id' => $field->id,
            'image_path' => $path,
        ]);
    }
    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('fields.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Edit Lapangan</x-slot>

    @include('layouts.tom-select')

    @volt
        <div>
            @foreach ($errors->all() as $item)
                <p>{{ $item }}</p>
            @endforeach
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit Lapangan</strong>
                        <p>Pada halaman edit Lapangan, kamu dapat mengubah informasi Lapangan yang sudah ada.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf

                        <div class="mb-3">
                            <label for="field_name" class="form-label">Nama Lapangan</label>
                            <input type="text" class="form-control @error('field_name') is-invalid @enderror"
                                wire:model="field_name" id="field_name" aria-describedby="field_nameId" autofocus
                                autocomplete="field_name" />
                            @error('field_name')
                                <small id="field_nameId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="facilities" class="form-label">Fasilitas</label>
                            <div wire:ignore>
                                <input type="text" wire:model="facilities" id="input-tags"
                                    aria-describedby="facilitiesId" value="{{ implode(',', $facilities) }}" autofocus
                                    autocomplete="facilities" />
                            </div>
                            @error('facilities')
                                <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Nama Lapangan</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror"
                                wire:model="images" id="images" aria-describedby="imagesId" autofocus
                                autocomplete="images" multiple />
                            @error('images')
                                <small id="imagesId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Lapangan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" name="description"
                                id="description" rows="3"></textarea>
                            @error('description')
                                <small id="descriptionId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
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
