<?php

use App\Models\User;
use function Livewire\Volt\{state, rules, uses};
use Illuminate\Validation\Rule;
use function Laravel\Folio\{name};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('catalogs.index');


?>

<x-guest-layout>

    @volt
        <div>
ini katalog
        </div>
    @endvolt
</x-guest-layout>
