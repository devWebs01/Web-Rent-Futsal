<?php

use App\Models\User;
use function Livewire\Volt\{state, uses, usesFileUploads};
use Illuminate\Validation\Rule;
use function Laravel\Folio\{name};
use Jantinnerezo\LivewireAlert\LivewireAlert;

usesFileUploads();

uses([LivewireAlert::class]);

name('profile.guest');

state([
    'user' => fn() => Auth()->user(),
    'name' => fn() => $this->user->name ?? '',
    'email' => fn() => $this->user->email ?? '',
    'phone' => fn() => $this->user->phone ?? '',
    'password',
]);

$edit = function () {
    $user = $this->user;

    $validateData = $this->validate([
        'name' => 'required|min:5',
        'email' => 'required|min:5|' . Rule::unique(User::class)->ignore($user->id),
        'password' => 'min:5|nullable',
        'phone' => 'required|digits_between:11,12|' . Rule::unique(User::class)->ignore($user->id),
    ]);

    $user = $this->user;

    // Jika wire:model password terisi, lakukan update password
    if (!empty($this->password)) {
        $validateData['password'] = bcrypt($this->password);
    } else {
        // Jika wire:model password tidak terisi, gunakan password yang lama
        $validateData['password'] = $user->password;
    }

    // Perbarui data pengguna
    $user->update($validateData);

    // Tampilkan notifikasi
    $this->alert('success', 'Proses berhasil!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('profile.guest');
};

?>

<x-guest-layout>
    <x-slot name="title">Profile User</x-slot>
    @include('layouts.fancybox')

    @volt
    <div>

        <div class="container">
            <div class="card border-0">
                <div class="alert alert-primary border-0 shadow" role="alert">
                    <div>
                        <strong>Data Profile</strong>
                        <p>Pada halaman edit pengguna, kamu dapat mengubah informasi pengguna.
                            Kamu tidak perlu menginputkan ulang kata sandi mu jika tidak ingin mengubah kata sandi.
                        </p>
                    </div>
                </div>

                <div class="card-body shadow rounded">

                    <form wire:submit="edit">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" id="name" aria-describedby="nameId"
                                        placeholder="Enter user name" autofocus autocomplete="name" />
                                    @error('name')
                                        <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email" id="email" aria-describedby="emailId"
                                        placeholder="Enter user email" />
                                    @error('email')
                                        <small id="emailId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telepon</label>
                                    <input type="number" class="form-control @error('phone') is-invalid @enderror"
                                        wire:model="phone" id="phone" aria-describedby="phoneId"
                                        placeholder="Enter user phone" />
                                    @error('phone')
                                        <small id="phoneId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Kata Sandi</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password" id="password" aria-describedby="passwordId"
                                        placeholder="Enter user password" />
                                    @error('password')
                                        <small id="passwordId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </div>

                            <div class="col-md-6 align-self-center text-end">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-guest-layout>