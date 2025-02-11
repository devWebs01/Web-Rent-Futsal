<?php

use App\Models\Gallery;
use function Livewire\Volt\{state};
use function Laravel\Folio\{name};

name('informations.blog');

state([
    'blogs' => fn() => Gallery::get(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Informasi Futsal</x-slot>

    @volt
        <div>


        </div>
    @endvolt
</x-guest-layout>
