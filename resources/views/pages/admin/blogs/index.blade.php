<?php

use App\Models\Blog;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, uses};

uses([LivewireAlert::class]);

name("blogs.index");

$blogs = computed(function () {
    return blog::query()->latest()->get();
});

$destroy = function (blog $blog) {
    try {
        $blog->delete();
        $this->alert("success", "Data blog berhasil dihapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        Log::error("Error deleting blog: " . $th->getMessage());
        $this->alert("error", "Data blog gagal dihapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }

    $this->redirectRoute("blogs.index");
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Blog</x-slot>

        @include("components.partials.datatables")

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route("blogs.create") }}" class="btn btn-primary">Tambah
                            Blog</a>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded p-4">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Thumbnail</th>
                                        <th>Judul</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->blogs as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>
                                                <img class="object-fit-cover rounded-circle"
                                                    src="{{ Storage::url($item->thumbnail) }}" alt="Thumbnail" width="50"
                                                    height="50">
                                            </td>
                                            <td>{{ $item->title }}</td>
                                            <!-- Menampilkan gambar thumbnail -->
                                            <td>
                                                <div>
                                                    <a href="{{ route("blogs.edit", ["blog" => $item->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>

                                                    <!-- Tombol Hapus -->
                                                    <form action="{{ route("blogs.destroy", $item->id) }}" method="POST"
                                                        style="display: inline-block;"
                                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus data ini?')">
                                                        @csrf
                                                        @method("DELETE")
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            {{ __("Hapus") }}
                                                        </button>
                                                    </form>
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
