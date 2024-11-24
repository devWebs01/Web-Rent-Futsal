<?php

use App\Models\User;
use function Livewire\Volt\{state};

state(['count' => fn() => User::count()]);

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    @volt
        <div>
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header">{{ __('Dashboard') }}</div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {{ __('You are logged in!') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
