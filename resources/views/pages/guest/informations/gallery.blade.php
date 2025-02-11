<?php

use App\Models\Gallery;
use function Livewire\Volt\{state};
use function Laravel\Folio\{name};

name('informations.gallery');

state([
    'galleries' => fn() => Gallery::get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Galeri Kami</x-slot>
    @include('layouts.fancybox')
    @volt
        <div>
            <div class="container-fluid px-5">
                <div class="row justify-content-center text-center mb-3 mb-md-5">
                    <div class="col-lg-8 col-xxl-7">
                        <span class="text-muted">Showcase</span>
                        <h2 class="display-5 fw-bold mb-3">Galeri Kami</h2>
                        <p class="lead">Nikmati fasilitas modern dan suasana yang menyenangkan saat bermain futsal bersama
                            teman-teman atau rekan kerja.</p>
                    </div>
                </div>
                <div class="row g-2">
                    @foreach ($galleries as $gallery)
                        <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                            <a class="d-block" data-fancybox='gallery' data-src="{{ Storage::url($gallery->image) }}"
                                data-caption="{{ $gallery->alt }}">
                                <img src="{{ Storage::url($gallery->image) }}" class="img-fluid rounded shadow-sm"
                                    alt="{{ $gallery->alt }}" style="object-fit: cover; aspect-ratio: 1/1; width: 100%;" />
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endvolt
</x-guest-layout>
