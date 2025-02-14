<?php

use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

usesFileUploads();

uses([LivewireAlert::class]);

name('blogs.create');

state([
    'title' => '',
    'slug' => '',
    'body' => '',
    'tag' => '',
    'thumbnail' => null,
]);

rules([
    'title' => 'required|string|max:255',
    'body' => 'required|string',
    'tag' => 'required|string',
    'thumbnail' => 'required|image', // Maksimal 2MB
]);

$create = function () {
    $this->slug = Str::slug($this->title); // Generate slug dari title

    $validateData = $this->validate();

    // Simpan thumbnail jika diupload
    if ($this->thumbnail) {
        $validateData['thumbnail'] = $this->thumbnail->store('blogs', 'public');
    }

    Blog::create($validateData);

    $this->reset();

    $this->alert('success', 'Blog berhasil ditambahkan!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('blogs.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Tambah Blog Baru</x-slot>

    @include('layouts.tom-select')

    @volt
        <div>
            @include('layouts.editor')
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah Blog</strong>
                        <p>Silakan isi form berikut untuk menambahkan blog baru.</p>
                    </div>
                </div>

                <div class="card-body">

                    @if ($thumbnail)
                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-thumbnail mb-3 rounded" width="100%"
                            height="300" style="object-fit: cover" alt="Preview Thumbnail">
                    @endif

                    <form wire:submit="create">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                wire:model="title" id="title" placeholder="Masukkan judul blog" />
                            @error('title')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tag" class="form-label">Tag</label>
                            <div wire:ignore>
                                <input type="text" id="input-tags" wire:model="tag" id="tag"
                                    placeholder="Masukkan tag blog (opsional)" />
                            </div>
                            @error('tag')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Konten</label>
                            <div wire:ignore>
                                <textarea class="form-control @error('body') is-invalid @enderror" wire:model="body" id="editor" rows="6"
                                    placeholder="Tulis isi blog">
                        {{ $body }}
                        </textarea>
                            </div>
                            @error('body')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                wire:model="thumbnail" id="thumbnail" />
                            @error('thumbnail')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
