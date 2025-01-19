<?php

use App\Models\Field;
use App\Models\Setting;
use App\Models\Schedule;
use function Livewire\Volt\{state, mount};
use function Laravel\Folio\{name};

name('welcome');

state([
    'fields' => fn() => Field::get(),
    'starts' => range(1, 5),
    'lowestCosts' => fn() => Schedule::select('type', DB::raw('MAX(cost) as lowest_cost'))->groupBy('type')->get(),
    'setting' => fn() => Setting::first(),
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
                                    <div class="row g-4 justify-content-center">
                                        <div class="col-12">
                                            <div class="rounded bg-light">
                                                <img src="img/about-1.png" class="img-fluid rounded w-100" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="counter-item bg-light rounded p-3 h-100">
                                                <div class="counter-counting">
                                                    <span class="text-primary fs-2 fw-bold"
                                                        data-toggle="counter-up">3</span>
                                                    <span class="h1 fw-bold text-primary">+</span>
                                                </div>
                                                <h4 class="mb-0 text-dark">Tahun Pengalaman</h4>
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="counter-item bg-light rounded p-3 h-100">
                                                <div class="counter-counting">
                                                    <span class="text-primary fs-2 fw-bold"
                                                        data-toggle="counter-up">500</span>
                                                    <span class="h1 fw-bold text-primary">+</span>
                                                </div>
                                                <h4 class="mb-0 text-dark">Pelanggan Puas</h4>
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="counter-item bg-light rounded p-3 h-100">
                                                <div class="counter-counting">
                                                    <span class="text-primary fs-2 fw-bold"
                                                        data-toggle="counter-up">500</span>
                                                    <span class="h1 fw-bold text-primary">+</span>
                                                </div>
                                                <h4 class="mb-0 text-dark">Pertandingan</h4>
                                            </div>
                                        </div>
                                    </div>
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
                    <h4 class="text-primary">Fitur Kami</h4>
                    <h1 class="display-4 mb-4">Lapangan Futsal yang Nyaman dan Berkualitas</h1>
                    <p class="mb-0">Kami menyediakan berbagai fasilitas untuk memastikan kenyamanan Anda selama bermain
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
                            <p class="mb-4 text-capitalize">Tim kami siap membantu Anda Kapan Saja Anda Ingin memesan</p>

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
                    <h4 class="text-primary">Pilih Lapangan</h4>
                    <h1 class="display-4 mb-4 text-capitalize">untuk bermain bersama teman-temanmu!</h1>
                    <p class="mb-0">Kami berkomitmen untuk memberikan layanan terbaik kepada semua pengguna lapangan
                        kami.
                        Temukan layanan-layanan unggulan yang kami tawarkan.
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($fields as $field)
                        <div class="col-md wow fadeInUp" data-wow-delay="0.2s">
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
                                        <a href="#" class="d-inline-block h4 mb-4 fw-bold">
                                            {{ $field->field_name }}
                                        </a>
                                        <p class="mb-4">
                                            {{ $field->description }}
                                        </p>
                                        <a class="btn btn-primary rounded-pill py-2 px-4"
                                            href="{{ route('catalogs.field', ['field' => $field->id]) }}">Lihat
                                            Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
        <!-- Service End -->

        <section class="container-fluid bg-light py-5">
            <div class="container">
                <div class="row justify-content-center text-center mb-2 mb-lg-4">
                    <div class="col-12 col-lg-8 col-xxl-7 text-center mx-auto">
                        <h5 class="fw-bold text-primary">Nikmati Permainan Futsal dengan </h5>
                        <h4 class="display-4 mb-4 text-capitalize">Harga Terjangkau!</h4>
                        <p class="mb-0">Kami menawarkan harga sewa lapangan futsal yang kompetitif dan transparan,
                            sehingga Anda dapat fokus pada permainan tanpa khawatir tentang biaya. </p>
                    </div>
                </div>
                <div class="row">
                    @foreach ($lowestCosts as $item)
                        <div class="col-md-4">
                            <div class="card text-center border rounded-5">
                                <button class="btn btn-outline-dark rounded-5" href="#" role="button">
                                    <div class="card-body py-5">
                                        <div class="mb-3 mx-auto">
                                            <i class='bx bx-football display-4'></i>
                                        </div>
                                        <h5 class="fw-bold">
                                            {{ __('type.' . $item->type) }}
                                        </h5>
                                        <div class="display-6 fw-bold my-4 text-primary">
                                            {{ formatRupiah($item->lowest_cost) }}
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>


        <!-- FAQs Start -->
        <div class="container-fluid faq-section py-5">
            <div class="container">
                <div class="row mt-5">
                    <div class="col-lg text-center">
                        <img src="https://images.pexels.com/photos/15673782/pexels-photo-15673782/free-photo-of-pria-berebut-bola-saat-pertandingan.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                            class="img-fluid rounded w-100 mb-3" style="width: 50px; height: 600px; object-fit: cover">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="display-6 fw-bold">
                            FAQs
                        </h2>
                        <p>Puluhan ribu teman baru sudah menantimu di lapangan, yuk sewa lapangan sekarang juga! </p>
                        <div class="accordion accordion-flush" id="accordion-flush">
                            <div class="accordion-item border mb-3 rounded-3">
                                <h5 class="accordion-header">
                                    <button class="accordion-button bg-white rounded-4 text-primary collapsed"
                                        style="font-weight:bold;" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Jam berapa lapangan futsal tersedia untuk sewa?
                                    </button>
                                </h5>
                                <div id="collapseTwo" class="accordion-collapse collapse show"
                                    data-bs-parent="#accordion-flush">
                                    <div class="accordion-body">
                                        <p>Lapangan futsal kami tersedia untuk sewa setiap hari dari pukul 08:00
                                            hingga
                                            22:00.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border mb-3 rounded-3">
                                <h5 class="accordion-header">
                                    <button class="accordion-button bg-white rounded-4 text-primary collapsed"
                                        style="font-weight:bold;" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        Bagaimana cara membatalkan sewa lapangan?
                                    </button>
                                </h5>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion-flush">
                                    <div class="accordion-body">
                                        <p>Anda dapat membatalkan sewa melalui aplikasi atau website kami. Pastikan
                                            untuk
                                            melakukannya setidaknya 24 jam sebelum waktu sewa.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border mb-3 rounded-3">
                                <h5 class="accordion-header">
                                    <button class="accordion-button bg-white rounded-4 text-primary collapsed"
                                        style="font-weight:bold;" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        Apakah ada biaya tambahan untuk menggunakan fasilitas lain?
                                    </button>
                                </h5>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion-flush">
                                    <div class="accordion-body">
                                        <p>Biaya tambahan akan dikenakan jika Anda menggunakan fasilitas tambahan
                                            seperti
                                            alat olahraga atau ruang ganti. Silakan cek detail pada saat sewa.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border mb-3 rounded-3">
                                <h5 class="accordion-header">
                                    <button class="accordion-button bg-white rounded-4 text-primary collapsed"
                                        style="font-weight:bold;" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFive" aria-expanded="false"
                                        aria-controls="collapseFive">
                                        Apakah saya bisa mengubah waktu sewa setelah konfirmasi?
                                    </button>
                                </h5>
                                <div id="collapseFive" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion-flush">
                                    <div class="accordion-body">
                                        <p>Ya, Anda dapat mengubah waktu sewa dengan menghubungi tim dukungan kami,
                                            namun
                                            hal ini tergantung pada ketersediaan lapangan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FAQs End -->

    </div>
    @endvolt
</x-guest-layout>
