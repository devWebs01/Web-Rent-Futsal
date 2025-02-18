<?php

use App\Models\Blog;
use function Livewire\Volt\{state};
use function Laravel\Folio\{name};

name('informations.index');

state([
    'blogs' => fn() => Blog::latest()->get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Informasi Futsal</x-slot>

    @volt
        <div class="container-fluid px-3">
            <div class="row justify-content-center text-center mb-3 mb-md-5">
                <div class="col-lg-8 col-xxl-7">
                    <span class="text-primary">Showcase</span>
                    <h2 class="display-5 fw-bold mb-3">Blog</h2>
                    <p class="lead">Ikuti blog kami untuk mendapatkan informasi terbaru, tips bermain futsal, dan berbagai
                        acara menarik yang kami selenggarakan.</p>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach ($blogs as $blog)
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="blog-item card h-100">
                            <div class="blog-img">
                                <img src="{{ Storage::url($blog->thumbnail) }}" style="width: 100%; height: 250px; object-fit: cover;" class="img rounded-top"
                                    alt="{{ $blog->title }}">
                            </div>
                            <div class="blog-content card-body d-flex flex-column">
                                <div class="blog-comment d-flex justify-content-between mb-3">
                                    <div class="small">
                                        <span class="fa fa-calendar text-primary"></span>
                                        {{ $blog->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                <a href="{{ route('informations.blog', ['blog' => $blog]) }}"
                                    class="h5 fw-bold d-inline-block mb-3">
                                    {{ $blog->title }}
                                </a>
                                <a href="{{ route('informations.blog', ['blog' => $blog]) }}" class="btn p-0 mt-auto">
                                    Baca Selengkapnya <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


        </div>
    @endvolt
</x-guest-layout>
