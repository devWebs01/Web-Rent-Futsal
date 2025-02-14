<?php

use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

usesFileUploads();

uses([LivewireAlert::class]);

name('galleries.edit');

state([
    'alt' => fn() => $this->gallery->alt,
    'gallery',
    'image',
]);

rules([
    'image' => 'required|image',
    'alt' => 'required|string',
]);

$edit = function () {
    $gallery = $this->gallery;

    // Validasi input
    $validateData = $this->validate();

    // Jika ada gambar baru yang di-upload
    if ($this->image) {
        // Hapus gambar lama
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }

        // Simpan gambar baru
        $newImage = $this->image->store('gallery', 'public');
        $validateData['image'] = $newImage;
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama
        $validateData['image'] = $gallery->image;
    }

    // Update data gallery
    $gallery->update($validateData);

    $gallery->update($validateData);

    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('galleries.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Edit gallery</x-slot>


    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit gallery</strong>
                        <p>Pada halaman edit gallery, kamu dapat mengubah informasi gallery yang sudah ada.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="img rounded mb-3" width="100%" height="500px"
                            style="object-fit: cover" alt="preview" />
                    @elseif(empty($image))
                        <small>Gambar tersimpan
                            <span class="text-danger">(Jika tidak mengubah gambar, tidak perlu melakukan
                                input gambar)</span>
                            .
                        </small>
                        <img src="{{ Storage::url($gallery->image) }}" class="img rounded mb-3" width="100%"
                            height="500px" style="object-fit: cover" alt="preview" />
                    @endif



                    <form wire:submit="edit">
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
