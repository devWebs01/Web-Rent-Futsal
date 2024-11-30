<?php

use App\Models\Setting;
use App\Models\Field;
use function Livewire\Volt\{state, rules, uses};
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

                <div class="row mb-5">
                    <h1 class="fw-bolder">
                        {{ $field->field_name }}
                        <span class="fs-5 badge bg-primary">{{ $field->status }}</span>

                    </h1>
                    <p class="text-muted mb-3">
                        {{ $setting->address }}
                    </p>

                    <div class="col-lg-8">

                        <h5 class="fw-bolder">
                            Deksripsi
                        </h5>
                        <p class="text-muted mb-3">
                            {{ $field->description }}
                        </p>


                        <h5 class="fw-bolder">
                            Fasilitas
                        </h5>
                        <div class="row mb-3">
                            @foreach ($field->facilities as $facility)
                                <div class="col-6">
                                    <p class="text-muted">
                                        -
                                        {{ $facility->facility_name }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div class="card border border-0">
                            <div class="card-body border rounded mb-3" style="height: 200px">

                            </div>
                            <div class="card-body border rounded" style="height: 100px">

                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    @endvolt
</x-guest-layout>
