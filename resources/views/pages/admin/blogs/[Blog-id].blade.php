<?php

use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

usesFileUploads();
uses([LivewireAlert::class]);

name("blogs.edit");

state([
    "title" => fn() => $this->blog->title,
    "slug" => fn() => $this->blog->slug,
    "tag" => fn() => $this->blog->tag,
    "body" => fn() => $this->blog->body,
    "thumbnail" => null,
    "blog",
]);

rules([
    "title" => "required|string|max:255",
    "body" => "required",
    "tag" => "required|string",
    "thumbnail" => "nullable|image|max:2048",
]);

$edit = function () {
    $blog = $this->blog;
    $validatedData = $this->validate();
    $validatedData["slug"] = \Illuminate\Support\Str::slug($validatedData["title"]);

    if ($this->thumbnail) {
        $validatedData["thumbnail"] = $this->thumbnail->store("thumbnails", "public");
    } else {
        $validatedData["thumbnail"] = $blog->thumbnail;
    }

    $blog->update($validatedData);

    $this->alert("success", "Blog berhasil diperbarui!", [
        "position" => "center",
        "timer" => 3000,
        "toast" => true,
    ]);

    $this->redirectRoute("blogs.index");
};

?>

<x-admin-layout>
    <x-slot name="title">Edit Blog</x-slot>
    @include("components.partials.tom-select")

    @volt
        @include("components.partials.editor")
        <div class="card">
            <div class="card-header">
                <div class="alert alert-primary" role="alert">
                    <strong>Edit Blog</strong>
                    <p>Pada halaman edit blog, kamu dapat mengubah informasi blog yang sudah ada.</p>
                </div>
            </div>
            <div class="card-body">
                <form wire:submit="edit">
                    @csrf

                    @if ($thumbnail)
                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-thumbnail mb-3 rounded" width="100%"
                            height="300" style="object-fit: cover" alt="Preview Thumbnail">
                    @elseif ($this->blog->thumbnail)
                        <img src="{{ asset("storage/" . $this->blog->thumbnail) }}" class="img-thumbnail mb-3 rounded"
                            width="100%" height="300" style="object-fit: cover" alt="Preview Thumbnail">
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control @error("title") is-invalid @enderror" wire:model="title"
                            id="title" placeholder="Masukkan judul blog" />
                        @error("title")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tag" class="form-label">Tag</label>
                        <div wire:ignore>
                            <input type="text" wire:model="tag" id="input-tags"
                                value="{{ is_array($tag) ? implode(",", $tag) : $tag }}" placeholder="Enter blog tag" />
                        </div>
                        @error("tag")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">Konten</label>
                        <div wire:ignore>
                            <textarea class="form-control @error("body") is-invalid @enderror" wire:model="body" id="editor" rows="6"
                                placeholder="Tulis isi blog">
                        {{ $body }}
                        </textarea>
                        </div>
                        @error("body")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail</label>
                        <input type="file" class="form-control @error("thumbnail") is-invalid @enderror"
                            wire:model="thumbnail" id="thumbnail" />
                        @error("thumbnail")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                    </div>
                </form>
            </div>
        </div>
    @endvolt
</x-admin-layout>
