<?php

use App\Models\Field;
use App\Models\Schedule;
use function Livewire\Volt\{state, mount};
use function Laravel\Folio\{name};

name('welcome');

state([
    'fields' => fn() => Field::get(),
    'starts' => range(1, 5),
    'lowestCosts' => fn() => Schedule::select('type', DB::raw('MAX(cost) as lowest_cost'))->groupBy('type')->get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Welcome</x-slot>

    @volt
        <div>
            <section>
                <div class="container-fluid">
                    <div class="banner rounded-4 p-5"
                        style="background-image: url('https://images.pexels.com/photos/14690057/pexels-photo-14690057.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-size: cover ; background-repeat: no-repeat; background-position: center;">
                        <div class="text-content text-white py-5 my-5">
                            <p class="fs-4">
                                Sewa Lapangan Futsal
                            </p>
                            <h1 class="display-1 fw-bold">
                                Futsal Arena <br> sewa Online
                            </h1>
                        </div>
                        <div class="row text-capitalize bg-black rounded-4 p-3 mt-5">
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="display-2 fw-bold text-light">
                                        10
                                    </h2>
                                    <p class="text-light justify-content-center m-0 ls-4">
                                        Tahun pengalaman
                                    </p>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="display-2 fw-bold text-light">
                                        500
                                    </h2>
                                    <p class="text-light justify-content-center m-0 ls-4">
                                        pelanggan puas
                                    </p>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="display-2 fw-bold text-light">
                                        2000
                                    </h2>
                                    <p class="text-light justify-content-center m-0 ls-4">
                                        pertandingan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-5">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg">
                            <div class="d-flex gap-4 align-items-start">
                                <div class="display-1 text-danger">
                                    <i class='bx bx-phone'></i>
                                </div>
                                <div class="text-md-start">
                                    <h5>
                                        Sewa Mudah
                                    </h5>
                                    <p>
                                        Proses sewa yang cepat dan mudah hanya dalam hitungan menit.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="d-flex gap-4 align-items-start">
                                <div class="display-1 text-danger">
                                    <i class='bx bx-credit-card'></i>
                                </div>
                                <div class="text-md-start">
                                    <h5>
                                        Pembayaran Aman
                                    </h5>
                                    <p>
                                        Metode pembayaran yang aman dan terpercaya untuk kenyamanan Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="d-flex gap-4 align-items-start">
                                <div class="display-1 text-danger">
                                    <i class='bx bx-user-voice'></i>
                                </div>
                                <div class="text-md-start">
                                    <h5>
                                        Dukungan
                                    </h5>
                                    <p>
                                        Tim dukungan siap membantu Anda kapan saja jika ada pertanyaan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="portfolio py-5" id="fields">
                <div class="container">
                    <div class="justify-content-center">

                        <div class="row justify-content-center">

                            <div class="col-lg-6" data-aos="fade-up">
                                <div class="section-header text-center">
                                    <span class="fw-bold text-danger">
                                        Pilih Lapangan
                                    </span>
                                    <h4 class="fw-bold fs-2 text-capitalize">
                                        untuk bermain bersama teman-temanmu!
                                    </h4>
                                </div><!--section-header-->
                            </div>
                        </div>

                        <div class="container">
                            <div class="row py-4">
                                @foreach ($fields as $field)
                                    <div class="col-lg-6 p-3">
                                        <div class="card p-3 border rounded-5">
                                            <a href="{{ route('catalogs.field', ['field' => $field->id]) }}"
                                                class="text-decoration-none">
                                                @if ($field->images->count() < 0)
                                                    <img src="https://images.pexels.com/photos/29388472/pexels-photo-29388472.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                                        class="card-omg-top rounded-4 w-100" width="200px" height="200px"
                                                        style="object-fit: cover;">
                                                @else
                                                    <img src="{{ Storage::url($field->images->first()->image_path) }}"
                                                        class="card-omg-top rounded-4 w-100" width="200px" height="200px"
                                                        style="object-fit: cover;">
                                                @endif
                                                <div class="card-body">
                                                    <h3 class="text-dark">
                                                        {{ $field->field_name }}
                                                    </h3>
                                                    @foreach ($starts as $start)
                                                        <i class='bx bxs-star text-warning'></i>
                                                    @endforeach
                                                    <p class=" text-muted mt-3">
                                                        {{ Str::limit($field->description, 180, '...') }}
                                                    </p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
            </section>

            <section class="pb-5">
                <div class="container">
                    <div class="row justify-content-center text-center mb-2 mb-lg-4">
                        <div class="col-12 col-lg-8 col-xxl-7 text-center mx-auto">
                            <span class="fw-bold text-danger">Nikmati Permainan Futsal dengan </span>
                            <h4 class="display-6 fw-bold">Harga Terjangkau!</h4>
                            <p class="lead">Kami menawarkan harga sewa lapangan futsal yang kompetitif dan transparan,
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
                                            <div class="display-6 fw-bold my-4 text-danger">
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

            <section>
                <div class="container py-5">
                    <div class="rounded-4 p-5"
                        style="background-image: url('https://images.pexels.com/photos/13521967/pexels-photo-13521967.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-size: cover ; background-repeat: no-repeat; background-position: center;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="h-100 bg-black text-white p-4 rounded-4">
                                    <p>Sewa lapangan online terbaik</p>
                                    <h1>
                                        AYO, Temukan Kawan Mainmu Sekarang!
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
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
                                        <button class="accordion-button bg-white rounded-4 text-danger collapsed"
                                            style="font-weight:bold;" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwo" aria-expanded="false"
                                            aria-controls="collapseTwo">
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
                                        <button class="accordion-button bg-white rounded-4 text-danger collapsed"
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
                                        <button class="accordion-button bg-white rounded-4 text-danger collapsed"
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
                                        <button class="accordion-button bg-white rounded-4 text-danger collapsed"
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
            </section>


        </div>
    @endvolt
</x-guest-layout>
