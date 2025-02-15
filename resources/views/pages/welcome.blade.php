<?php

use App\Models\Field;
use App\Models\Setting;
use App\Models\Blog;
use function Livewire\Volt\{state};
use function Laravel\Folio\{name};

name('welcome');

state([
    'fields' => fn() => Field::get(),
    'starts' => range(1, 5),
    'setting' => fn() => Setting::first(),
    'blogs' => Blog::inRandomOrder()->limit(4)->get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Selamat Datang di FutsalKu</x-slot>
    {{-- @include('layouts.fancybox') --}}
    @volt
        <div>

            <!-- Carousel Start -->
            <div class=" rounded header-carousel owl-carousel">
                <div class="header-carousel-item"
                    style="background-image: url('https://images.pexels.com/photos/14690057/pexels-photo-14690057.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-size: cover ; background-repeat: no-repeat; background-position: center;">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row g-4 align-items-center">
                                <div class="col-12 animated fadeInLeft">
                                    <div class="text-start">
                                        <h5 class="text-white text-uppercase fw-bold mb-4">Selamat Datang di
                                            {{ $setting->name }}
                                        </h5>
                                        <h1 class="display-1 text-white mb-4 col-6">Sewa Lapangan Futsal Terbaik</h1>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Carousel End -->

            <!-- Feature Start -->
            <div class="container-fluid feature bg-light py-5">
                <div class="container py-5">
                    <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                        <span class="text-primary">Fitur Kami</span>
                        <h2 class="display-5 fw-bold mb-3">Lapangan Futsal yang Nyaman dan Berkualitas</h2>
                        <p class="lead">Kami menyediakan berbagai fasilitas untuk memastikan kenyamanan Anda selama
                            bermain
                            di lapangan futsal kami. Temukan fitur-fitur unggulan yang kami tawarkan.
                        </p>
                    </div>
                    <div class="row g-4 justify-content-between">
                        <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="feature-item p-4 pt-0">
                                <div class="feature-icon p-4 mb-4">
                                    <i class="far fa-handshake fa-3x"></i>
                                </div>
                                <h4 class="mb-4">Sewa Mudah</h4>
                                <p class="mb-4 text-capitalize">Proses sewa yang cepat dan mudah hanya dalam hitungan menit.
                                </p>

                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                            <div class="feature-item p-4 pt-0">
                                <div class="feature-icon p-4 mb-4">
                                    <i class="fa fa-dollar-sign fa-3x"></i>
                                </div>
                                <h4 class="mb-4">Pembayaran Aman</h4>
                                <p class="mb-4 text-capitalize">
                                    Metode pembayaran yang aman dan terpercaya.
                                </p>

                            </div>
                        </div>

                        <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                            <div class="feature-item p-4 pt-0">
                                <div class="feature-icon p-4 mb-4">
                                    <i class="fa fa-headphones fa-3x"></i>
                                </div>
                                <h4 class="mb-4">Dukungan Penuh</h4>
                                <p class="mb-4 text-capitalize">Tim kami siap membantu Anda Kapan Saja Anda Ingin memesan
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Feature End -->

            <!-- Service Start -->
            <div class="container-fluid service py-5" id="fields">
                <div class="container py-5">
                    <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                        <span class="text-primary">Pilih Lapangan</span>
                        <h2 class="display-5 fw-bold mb-3 text-capitalize">untuk bermain bersama teman-temanmu!</h2>
                        <p class="lead">Kami berkomitmen untuk memberikan layanan terbaik kepada semua pengguna lapangan
                            kami.
                            Temukan layanan-layanan unggulan yang kami tawarkan.
                        </p>
                    </div>
                    <div class="row g-4 justify-content-center">
                        @foreach ($fields as $field)
                            <div class="col-md wow fadeInUp" data-wow-delay="0.2s">
                                <a href="{{ route('catalogs.field', ['field' => $field->id]) }}">
                                    <div class="service-item">


                                        <div class="service-img">
                                            @if ($field->images->count() < 0)
                                                <img src="https://images.pexels.com/photos/29388472/pexels-photo-29388472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                                    class="card-omg-top rounded-4 w-100" width="200px" height="200px"
                                                    style="object-fit: cover;">
                                            @else
                                                <img src="{{ Storage::url($field->images->first()->image_path) }}"
                                                    class="card-omg-top rounded-4 w-100" width="200px" height="200px"
                                                    style="object-fit: cover;">
                                            @endif
                                            <div class="service-icon p-3">
                                                <i class="fa fa-users fa-2x"></i>
                                            </div>
                                        </div>
                                        <div class="service-content p-4">
                                            <div class="service-content-inner">
                                                <span class="d-inline-block h4 mb-4 fw-bold">
                                                    {{ $field->field_name }}
                                                </span>
                                                <p class="mb-4">
                                                    {{ $field->description }}
                                                </p>
                                                <a class="btn btn-primary rounded-pill py-2 px-4"
                                                    href="{{ route('catalogs.field', ['field' => $field->id]) }}">Lihat
                                                    Selengkapnya</a>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            <!-- Service End -->

            @include('pages.scheduleTable')

            <div class="container-fluid blog py-5">
                <div class="px-3 py-5">
                    <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s"
                        style="max-width: 800px; visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                        <span class="text-primary">Showcase</span>
                        <h2 class="display-5 fw-bold mb-3">Blog</h2>
                        <p class="lead">Ikuti blog kami untuk mendapatkan informasi terbaru, tips bermain futsal, dan
                            berbagai
                            acara menarik yang kami selenggarakan.</p>
                    </div>
                    <div class="row g-4 justify-content-center">
                        @foreach ($blogs as $blog)
                            <div class="col-md-3 wow fadeInUp" data-wow-delay="0.2s">
                                <div class="blog-item card h-100">
                                    <div class="blog-img">
                                        <img src="{{ Storage::url($blog->thumbnail) }}"
                                            class="img-fluid rounded-top w-100" alt="{{ $blog->title }}">
                                    </div>
                                    <div class="blog-content card-body d-flex flex-column">
                                        <div class="blog-comment d-flex justify-content-between mb-3">
                                            <div class="small">
                                                <span class="fa fa-calendar text-primary"></span>
                                                {{ $blog->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a href="{{ route('informations.blog', ['blog' => $blog]) }}"
                                            class="h4 d-inline-block mb-3">
                                            {{ $blog->title }}
                                        </a>
                                        <a href="{{ route('informations.blog', ['blog' => $blog]) }}"
                                            class="btn p-0 mt-auto">
                                            Baca Selengkapnya <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    @endvolt
</x-guest-layout>
