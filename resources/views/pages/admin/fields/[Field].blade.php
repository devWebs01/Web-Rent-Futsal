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

name("fields.edit");

state([
    "field_name" => fn() => $this->field->field_name,
    "description" => fn() => $this->field->description,
    "facilities" => fn() => $this->field->facilities->pluck("facility_name")->toArray(), // Muat facilities sebagai array
    "images" => [],
    "field",
]);

rules([
    "field_name" => "required|string|min:5",
    "description" => "required|string|min:5",
    "facilities" => "required", // Validasi array
    "facilities.*" => "required|string|min:5", // Validasi setiap item
    "images" => "nullable", // Validasi file gambar
    "images.*" => "image|max:2048", // Validasi file gambar
]);

$updatingImages = function ($value) {
    $this->previmages = $this->images;
};

$updatedImages = function ($value) {
    $this->images = array_merge($this->previmages, $value);
};

$removeItem = function ($key) {
    if (isset($this->images[$key])) {
        $file = $this->images[$key];
        $file->delete();
        unset($this->images[$key]);
    }

    $this->images = array_values($this->images);
};

$edit = function () {
    $field = $this->field;

    $validateData = $this->validate();

    try {
        // Mulai transaksi database
        \DB::beginTransaction();

        // Update field
        $field->update([
            "field_name" => $validateData["field_name"],
            "description" => $validateData["description"],
        ]);

        // Pastikan facilities berupa array
        $facilities = is_array($this->facilities) ? $this->facilities : explode(",", $this->facilities);

        // Hapus fasilitas lama dan tambahkan yang baru
        $field->facilities()->delete();

        foreach ($facilities as $facility) {
            $field->facilities()->create([
                "facility_name" => $facility,
            ]);
        }

        if (count($this->images) > 0) {
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

            foreach ($this->images as $image) {
                $path = $image->store("fields", "public"); // Simpan ke folder "fields" di storage
                Image::create([
                    "field_id" => $field->id,
                    "image_path" => $path,
                ]);

                $image->delete();
            }
        }

        // Commit transaksi
        \DB::commit();

        $this->alert("success", "Data berhasil diedit!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);

        $this->redirectRoute("fields.index");
    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        \DB::rollBack();

        // Tampilkan notifikasi error
        $this->alert("error", "Terjadi kesalahan saat menyimpan data!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }
};

?>

<x-admin-layout>
    <x-slot name="title">Edit Lapangan</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route("home") }}">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route("fields.index") }}">Lapangan</a>
        </li>
        <li class="breadcrumb-item">
            <a href="#">Edit Lapangan</a>
        </li>
    </x-slot>

    @include("components.partials.tom-select")

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit Lapangan</strong>
                        <p>
                            Pada halaman edit Lapangan, kamu dapat mengubah informasi Lapangan yang sudah ada.
                        </p>
                    </div>
                </div>
                @if ($images)
                    <div class="card-body">
                        <div class="d-flex flex-nowrap gap-3 overflow-auto" style="white-space: nowrap;">
                            @foreach ($images as $key => $image)
                                <div class="position-relative" style="width: 200px; flex: 0 0 auto;">
                                    <div class="card mt-5">
                                        <div class="card-img-top">
                                            <img src="{{ $image->temporaryUrl() }}" class="img"
                                                style="object-fit: cover;" width="200px" height="200px" alt="preview">
                                            <a type="button" class="position-absolute top-0 start-100 translate-middle p-2"
                                                wire:click.prevent='removeItem({{ json_encode($key) }})'>
                                                <i class="bx bx-x p-2 rounded-circle text-white bg-danger"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-center">
                                            {{ Str::limit($image->getClientOriginalName(), 20, "...") }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif ($field->images->isNotEmpty())
                    <div class="card-body">
                        <small>Gambar tersimpan
                            <span class="text-danger">(Jika tidak mengubah gambar, tidak perlu melakukan
                                input gambar)</span>
                            .
                        </small>
                        <div class="d-flex flex-nowrap gap-3 overflow-auto" style="white-space: nowrap;">
                            @foreach ($field->images as $key => $image)
                                <div class="position-relative" style="width: 200px; flex: 0 0 auto;">
                                    <div class="card mt-3">
                                        <div class="card-img-top">
                                            <img src="{{ Storage::url($image->image_path) }}" class="img"
                                                style="object-fit: cover;" width="200px" height="200px" alt="preview">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="field_name" class="form-label">Nama Lapangan</label>
                                    <input type="text" class="form-control @error("field_name") is-invalid @enderror"
                                        wire:model="field_name" id="field_name" aria-describedby="field_nameId" autofocus
                                        autocomplete="field_name" />
                                    @error("field_name")
                                        <small id="field_nameId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Gambar Lapangan</label>
                                    <input type="file" class="form-control @error("images") is-invalid @enderror"
                                        wire:model="images" id="images" aria-describedby="imagesId" accept="image/*"
                                        autocomplete="images" multiple />
                                    @error("images")
                                        <small id="imagesId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="facilities" class="form-label">Fasilitas</label>
                            <div wire:ignore>
                                <input type="text" wire:model="facilities" id="input-tags"
                                    aria-describedby="facilitiesId" value="{{ implode(",", $facilities) }}"
                                    autocomplete="facilities" />
                            </div>
                            @error("facilities")
                                <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                            <br>
                            @error("facilities.*")
                                <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Lapangan</label>
                            <textarea class="form-control @error("description") is-invalid @enderror" wire:model="description" name="description"
                                id="description" rows="3"></textarea>
                            @error("description")
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
