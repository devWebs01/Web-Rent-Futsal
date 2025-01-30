<?php

use App\Models\User;
use function Livewire\Volt\{computed, state, usesPagination, uses};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('customers');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$users = computed(function () {
    if ($this->search == null) {
        return User::query()->where('role', 'customer')->latest()->paginate(10);
    } else {
        return User::query()
            ->where('role', 'customer')
            ->where(function ($query) {
                $query
                    ->where('name', 'LIKE', "%{$this->search}%")
                    ->orWhere('email', 'LIKE', "%{$this->search}%")
                    ->orWhere('phone', 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

$destroy = function (User $user) {
    try {
        $user->delete();
        $this->alert('success', 'Data user berhasil di hapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data user gagal di hapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Pelanggan</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('customers') }}">Pelanggan</a>
            </li>

        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="mb-3">
                            <label for="email" class="form-label">Cari Pelanggan</label>
                            <input wire:model.live="search" type="search" class="form-control" name="search" id="search"
                                aria-describedby="helpId" placeholder="Masukkan nama pengguna / email / phone" />
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded">
                            <table class="table text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>phone</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->users as $no => $user)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>
                                                <button wire:loading.attr='disabled'
                                                    wire:click='destroy({{ $user->id }})' class="btn btn-sm btn-danger">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $this->users->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
