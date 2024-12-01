<?php

use App\Models\Field;
use function Livewire\Volt\{state, mount};
use function Laravel\Folio\{name};

name('welcome');

state(['fields' => fn() => Field::get()]);

?>

<x-guest-layout>
    @volt
        <div>
            <section>
                <div class="container">
                    <div class="banner rounded-4 p-5"
                        style="background-image: url('https://images.pexels.com/photos/14690057/pexels-photo-14690057.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-size: cover ; background-repeat: no-repeat; background-position: center;">
                        <div class="text-content text-white py-5 my-5">
                            <p class="fs-4">
                                Booking Lapangan Futsal
                            </p>
                            <h1 class="display-1">
                                Futsal Arena <br> Booking Online
                            </h1>
                        </div>
                        <div class="row text-uppercase bg-black rounded-4 p-3 mt-5">
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="display-2 text-light">
                                        10
                                    </h2>
                                    <p class="text-light-emphasis justify-content-center m-0 ls-4">
                                        Tahun pengalaman <br> dalam manajemen lapangan
                                    </p>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="display-2 text-light">
                                        500
                                    </h2>
                                    <p class="text-light-emphasis justify-content-center m-0 ls-4">
                                        pelanggan puas <br> setiap bulan
                                    </p>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="display-2 text-light">
                                        2000
                                    </h2>
                                    <p class="text-light-emphasis justify-content-center m-0 ls-4">
                                        pertandingan telah <br> diadakan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="p-5 ">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg">
                            <div class="d-flex gap-4 align-items-start">
                                <div class="icon">
                                    <svg class="text-primary monitor" width="50" height="50">
                                        <use xlink:href="#monitor"></use>
                                    </svg>
                                </div>
                                <div class="text-md-start">
                                    <h5>
                                        Booking Mudah
                                    </h5>
                                    <p class="postf">
                                        Proses booking yang cepat dan mudah hanya dalam hitungan menit.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="d-flex gap-4 align-items-start">
                                <div class="icon">
                                    <svg class="text-primary notes" width="50" height="50">
                                        <use xlink:href="#notes"></use>
                                    </svg>
                                </div>
                                <div class="text-md-start">
                                    <h5>
                                        Pembayaran Aman
                                    </h5>
                                    <p class="postf">
                                        Metode pembayaran yang aman dan terpercaya untuk kenyamanan Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="d-flex gap-4 align-items-start">
                                <div class="icon">
                                    <svg class="text-primary laptop" width="50" height="50">
                                        <use xlink:href="#laptop"></use>
                                    </svg>
                                </div>
                                <div class="text-md-start">
                                    <h5>
                                        Dukungan Pelanggan
                                    </h5>
                                    <p class="postf">
                                        Tim dukungan siap membantu Anda kapan saja jika ada pertanyaan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="portfolio py-5">
                <div class="container">
                    <div class="justify-content-center">

                        <div class="row justify-content-center">

                            <div class="col-lg-6" data-aos="fade-up">
                                <div class="section-header text-center">
                                    <h4 class="fw-bold fs-2 txt-fx slide-up">
                                        Sewa lapangan untuk bermain bersama teman-temanmu!
                                    </h4>
                                </div><!--section-header-->
                            </div>
                        </div>

                        <div class="container">
                            <div class="row py-4">
                                @foreach ($fields as $field)
                                    <div class="col-lg-6 p-3">
                                        <div class="post-item p-3 border rounded-5">
                                            <a href="{{ route('catalogs.field', ['field' => $field->id]) }}"
                                                class="text-decoration-none">
                                                <div class="row g-md-5">
                                                    <div class="col-lg-5">
                                                        @if ($field->images->count() < 0)
                                                            <img src="https://images.pexels.com/photos/29388472/pexels-photo-29388472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                                                class="img rounded-4 w-100" width="200px" height="200px"
                                                                style="object-fit: cover;">
                                                        @else
                                                            <img src="{{ Storage::url($field->images->first()->image_path) }}"
                                                                class="img rounded-4 w-100" width="200px" height="200px"
                                                                style="object-fit: cover;">
                                                        @endif
                                                    </div>
                                                    <div class="col-lg-7 ">
                                                        <h3>
                                                            {{ $field->field_name }}

                                                        </h3>
                                                        <p class=" text-muted mt-3">
                                                            {{ Str::limit($field->description, 180, '...') }}
                                                        </p>
                                                    </div>

                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
            </section>


            <section>
                <div class="container">
                    <div class="rounded-4 p-5"
                        style="background-image: url('https://images.pexels.com/photos/13521967/pexels-photo-13521967.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-size: cover ; background-repeat: no-repeat; background-position: center;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="h-100 bg-black text-white p-4 rounded-4">
                                    <h1>
                                        AYO, Temukan Kawan Mainmu Sekarang!
                                    </h1>
                                    <div class="py-4">
                                        <p class="text-light-emphasis">
                                            Puluhan ribu teman baru sudah menantimu di lapangan, yuk booking lapangan
                                            sekarang juga!
                                        </p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="container pt-5">
                    <div class="row">
                        <div class="col-md">
                            <div class="h-100 bg-green p-4 rounded-4">
                                <h3>
                                    Pengalaman Pelanggan
                                </h3>
                                <div class="py-4">
                                    <h5>
                                        Manajer Lapangan Futsal
                                    </h5>
                                    <p class="text-dark-emphasis">
                                        Meningkatkan kepuasan pelanggan melalui layanan yang responsif.
                                    </p>
                                    <h5>
                                        Pemilik Futsal Arena
                                    </h5>
                                    <p class="text-dark-emphasis">
                                        Mengelola dan mengembangkan bisnis futsal dengan layanan terbaik.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md">
                            <div class="h-100 bg-teal p-4 rounded-4">
                                <h3>
                                    Testimoni
                                </h3>
                                <div class="py-4">
                                    <p class="text-dark-emphasis">
                                        "Lapangan futsal terbaik yang pernah saya coba! Sangat direkomendasikan."
                                    </p>
                                    <p class="text-dark-emphasis">
                                        "Proses booking yang sangat mudah dan cepat."
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section>
                <div class="container">
                    <div class="text-center pt-5">
                        <h2 class="display-3">
                            FAQs
                        </h2>
                    </div>
                    <div class="row mt-5">
                        <div class="col-lg text-center">
                            <img src="https://images.pexels.com/photos/15673782/pexels-photo-15673782/free-photo-of-pria-berebut-bola-saat-pertandingan.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                class="img-fluid rounded w-100 mb-3"
                                style="width: 50px; height: 450px; object-fit: cover">
                        </div>
                        <div class="col-lg-6">
                            <div class="accordion accordion-flush" id="accordion-flush">
                                <div class="accordion-item border mb-3 rounded-3">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-weight:bold;"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            Apakah saya bisa menggunakan kartu kredit untuk pembayaran?
                                        </button>
                                    </h5>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordion-flush">
                                        <div class="accordion-body">
                                            <p>Ya, kami menerima pembayaran menggunakan kartu kredit dan metode pembayaran
                                                lainnya untuk kenyamanan Anda.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border mb-3 rounded-3">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-weight:bold;"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            Jam berapa lapangan futsal tersedia untuk booking?
                                        </button>
                                    </h5>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        data-bs-parent="#accordion-flush">
                                        <div class="accordion-body">
                                            <p>Lapangan futsal kami tersedia untuk booking setiap hari dari pukul 08:00
                                                hingga
                                                22:00.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border mb-3 rounded-3">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-weight:bold;"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            Bagaimana cara membatalkan booking lapangan?
                                        </button>
                                    </h5>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        data-bs-parent="#accordion-flush">
                                        <div class="accordion-body">
                                            <p>Anda dapat membatalkan booking melalui aplikasi atau website kami. Pastikan
                                                untuk
                                                melakukannya setidaknya 24 jam sebelum waktu booking.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border mb-3 rounded-3">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-weight:bold;"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                            aria-expanded="false" aria-controls="collapseFour">
                                            Apakah ada biaya tambahan untuk menggunakan fasilitas lain?
                                        </button>
                                    </h5>
                                    <div id="collapseFour" class="accordion-collapse collapse"
                                        data-bs-parent="#accordion-flush">
                                        <div class="accordion-body">
                                            <p>Biaya tambahan akan dikenakan jika Anda menggunakan fasilitas tambahan
                                                seperti
                                                alat olahraga atau ruang ganti. Silakan cek detail pada saat booking.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border mb-3 rounded-3">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-weight:bold;"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                            aria-expanded="false" aria-controls="collapseFive">
                                            Apakah saya bisa mengubah waktu booking setelah konfirmasi?
                                        </button>
                                    </h5>
                                    <div id="collapseFive" class="accordion-collapse collapse"
                                        data-bs-parent="#accordion-flush">
                                        <div class="accordion-body">
                                            <p>Ya, Anda dapat mengubah waktu booking dengan menghubungi tim dukungan kami,
                                                namun
                                                hal ini tergantung pada ketersediaan lapangan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endvolt
</x-guest-layout>
