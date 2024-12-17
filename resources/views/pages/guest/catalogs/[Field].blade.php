<?php

use App\Models\Setting;
use App\Models\schedule;
use App\Models\Field;
use function Livewire\Volt\{state, computed, uses};
use Illuminate\Validation\Rule;
use function Laravel\Folio\{name};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('catalogs.field');

state([
    'setting' => fn() => Setting::first(),
    'field',
]);

?>

<x-guest-layout>

    @volt
        <div>
            <section class="container">
                <div id="carouselExampleIndicators" class="carousel slide">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner mb-3 rounded-5">
                        @if ($field->images->count() > 0)
                            @foreach ($field->images as $index => $image)
                                <div class="carousel-item {{ $index !== 0 ?: 'active' }}">
                                    <img src="{{ Storage::url($image->image_path) }}" class="d-block object-fit-cover"
                                        width="100%" height="600px" alt="...">
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <img src="https://images.pexels.com/photos/15818644/pexels-photo-15818644/free-photo-of-bidang-lahan-padang-lapangan.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                    class="d-block object-fit-cover" width="100%" height="600px" alt="...">
                            </div>
                        @endif

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </section>

            <section class="container">
                <div class="row mb-5 mx-2">
                    <h1 class="fw-bolder">
                        {{ $field->field_name }}
                    </h1>
                    <p>
                        <span class="fs-5 badge bg-dark">{{ __('status.' . $field->status) }}</span>
                    </p>
                    <p class="postf mb-3">
                        {{ $setting->address }}
                    </p>

                    <div class="col-lg-8 col-md-12">

                        <h5 class="fw-bolder">
                            Deksripsi
                        </h5>
                        <p class="postf mb-3">
                            {{ $field->description }}
                        </p>


                        <h5 class="fw-bolder">
                            Fasilitas
                        </h5>
                        <div class="row mb-3">
                            @foreach ($field->facilities as $facility)
                                <div class="col-6">
                                    <p class="postf">
                                        -
                                        {{ $facility->facility_name }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="row border border-0">
                            <div class="col-12">
                                <h5>Sewa lapangan kini lebih menyenangkan!</h5>
                                <ul>
                                    <li class="postf">
                                        Proses booking yang cepat dan mudah hanya dalam hitungan menit.</li>
                                    <li class="postf">
                                        Metode pembayaran yang aman dan terpercaya untuk kenyamanan Anda.</li>
                                    <li class="postf">
                                        Tim dukungan siap membantu Anda kapan saja jika ada pertanyaan.</li>
                                </ul>
                                <a type="button" class="text-decoration-underline text-danger fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#modalId">
                                    Syarat & Ketentuan Berlaku
                                </a>

                                <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static"
                                    data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
                                        role="document">
                                        <div class="modal-content">
                                            <div class="modal-header border border-0">
                                                <h5 class="modal-title" id="modalTitleId">
                                                    Syarat & Ketentuan Berlaku
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="accordion" id="sewaRulesAccordion">


                                                    <!-- Kategori Pengguna -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwo">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                                aria-expanded="false" aria-controls="collapseTwo">
                                                                1. Kategori Pengguna
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwo" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingTwo"
                                                            data-bs-parent="#sewaRulesAccordion">
                                                            <div class="accordion-body">
                                                                <ul>
                                                                    <li><strong>Pelajar:</strong> Berlaku untuk siswa SD/SMP
                                                                        (harus menunjukkan kartu identitas).</li>
                                                                    <li><strong>Umum:</strong> Berlaku untuk semua kalangan
                                                                        selain kategori pelajar.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Pembayaran -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThree">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                                aria-expanded="false" aria-controls="collapseThree">
                                                                2. Pembayaran
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThree" class="accordion-collapse collapse"
                                                            aria-labelledby="headingThree"
                                                            data-bs-parent="#sewaRulesAccordion">
                                                            <div class="accordion-body">
                                                                <ul>
                                                                    <li>DP minimal <strong>50%</strong> dari total biaya.
                                                                    </li>
                                                                    <li>Pelunasan dilakukan sebelum sesi dimulai.</li>
                                                                    <li>Tidak ada pengembalian uang untuk pembatalan.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Ketentuan Pemesanan -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingFour">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                                aria-expanded="false" aria-controls="collapseFour">
                                                                3. Ketentuan Pemesanan
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFour" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#sewaRulesAccordion">
                                                            <div class="accordion-body">
                                                                <ul>
                                                                    <li><strong>Tidak Ada Pembatalan:</strong> Pemesanan
                                                                        yang sudah dibayar tidak dapat dibatalkan.</li>
                                                                    <li><strong>Perubahan Waktu:</strong> Tidak
                                                                        diperkenankan mengubah jadwal setelah konfirmasi.
                                                                    </li>
                                                                    <li><strong>Kehadiran Tepat Waktu:</strong> Penyewa
                                                                        diwajibkan datang tepat waktu sesuai jadwal yang
                                                                        sudah disepakati.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Sanksi -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingFive">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                                aria-expanded="false" aria-controls="collapseFive">
                                                                4. Sanksi
                                                            </button>
                                                        </h2>
                                                        <div id="collapseFive" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFive"
                                                            data-bs-parent="#sewaRulesAccordion">
                                                            <div class="accordion-body">
                                                                <ul>
                                                                    <li>Penyewa yang datang terlambat tidak diberikan
                                                                        perpanjangan waktu atau pengembalian dana.</li>
                                                                    <li>Penyewa yang tidak hadir sesuai jadwal tetap
                                                                        dikenakan biaya penuh.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Penjadwalan dan Reservasi -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingSeven">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseSeven"
                                                                aria-expanded="false" aria-controls="collapseSeven">
                                                                5. Penjadwalan dan Reservasi
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSeven" class="accordion-collapse collapse"
                                                            aria-labelledby="headingSeven"
                                                            data-bs-parent="#sewaRulesAccordion">
                                                            <div class="accordion-body">
                                                                Pemesanan dilakukan melalui sistem reservasi online atau
                                                                langsung di tempat dengan ketersediaan waktu sesuai jadwal
                                                                yang tersedia.
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
                    </div>
                </div>
            </section>

            <section class="container">
                @include('pages.guest.catalogs.priceList')
            </section>
        </div>
    @endvolt
</x-guest-layout>
