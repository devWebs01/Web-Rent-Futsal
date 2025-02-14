<?php

use App\Models\Gallery;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

usesFileUploads();

uses([LivewireAlert::class]);

name('galleries.create');

state(['image', 'alt']);

rules([
    'image' => 'required|image',
    'alt' => 'required|string',
]);

$create = function () {
    $validateData = $this->validate();

    $path = $this->image->store('gallery', 'public'); // Simpan ke folder "gallery" di storage
    $validateData['image'] = $path;

    gallery::create($validateData);

    $this->reset();

    $this->alert('success', 'Data berhasil ditambahkan!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('galleries.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Tambah Galeri Baru</x-slot>


    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah Galeri</strong>
                        <p>Pada halaman tambah gallery, kamu dapat memasukkan informasi dari gallery baru yang akan disimpan
                            ke
                            sistem.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="img rounded mb-3" width="100%" height="500px"
                            style="object-fit: cover" alt="preview" />
                    @endif
                    <form wire:submit="create">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        wire:model="image" id="image" aria-describedby="imageId"
                                        placeholder="Enter gallery image" autofocus autocomplete="image" />
                                    @error('image')
                                        <small id="imageId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="alt" class="form-label">Keterangan</label>
                                    <input type="text" class="form-control @error('alt') is-invalid @enderror"
                                        wire:model="alt" id="alt" aria-describedby="altId"
                                        placeholder="Enter gallery alt" />
                                    @error('alt')
                                        <small id="altId" class="form-text text-danger">{{ $message }}</small>
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
