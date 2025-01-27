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
    'dob' => fn() => $this->user->dob ?? '',
    'identity',
    'password',
]);

$checkAuth = function () {
    $user = Auth::user();

    if (!$user) {
        return false; // Tidak ada pengguna yang sedang login
    }

    if (empty($user->dob) || empty($user->identity)) {
        return false; // Tanggal lahir atau identitas kosong
    }

    return true; // Semua data tersedia
};

$edit = function () {
    $user = $this->user;

    $validateData = $this->validate([
        'name' => 'required|min:5',
        'email' => 'required|min:5|' . Rule::unique(User::class)->ignore($user->id),
        'password' => 'min:5|nullable',
        'phone' => 'required|digits_between:11,12|' . Rule::unique(User::class)->ignore($user->id),
        'dob' => 'required|date',
        'identity' => 'required|image',
    ]);

    $user = $this->user;

    // Jika wire:model password terisi, lakukan update password
    if (!empty($this->password)) {
        $validateData['password'] = bcrypt($this->password);
    } else {
        // Jika wire:model password tidak terisi, gunakan password yang lama
        $validateData['password'] = $user->password;
    }

    if ($this->identity) {
        // Hapus file lama jika ada
        if ($user->identity && Storage::exists($user->identity)) {
            Storage::delete($user->identity);
        }

        // Simpan file baru
        $path = $this->identity->store('identity', 'public'); // Simpan ke folder "identity" di disk "public"
        $validateData['identity'] = $path;
    } else {
        // Jika tidak ada file baru, gunakan file lama
        $validateData['identity'] = $user->identity;
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

                <div class="alert alert-danger {{ !$this->checkAuth() ?: 'd-none' }}" role="alert">
                    <div>
                        <strong>Peringatan</strong>
                        <p>Tolong lengkapi tanggal
                            lahir dan identitas diri untuk verifikasi penyewaan lapangan.</p>
                    </div>
                </div>


                <div class="card-body shadow rounded">
                    @if ($identity)

                        {{-- Jika $identity adalah file sementara --}}
                        <div class="card-img-top mb-5 rounded">
                            <a href="{{ $identity->temporaryUrl() }}" data-fancybox data-caption="Identitas baru">
                                <img src="{{ $identity->temporaryUrl() }}" class="img" style="object-fit: cover;"
                                    width="100%" height="200px" alt="Temporary Preview">
                            </a>
                        </div>

                    @elseif (!empty($user->identity))

                        {{-- Jika $identity adalah path file dari storage --}}
                        <div class="card-img-top mb-5 rounded">
                            <a href="{{ Storage::url($user->identity) }}" data-fancybox data-caption="Identitas user">
                                <img src="{{ Storage::url($user->identity) }}" class="img" style="object-fit: cover;"
                                    width="100%" height="200px" alt="Existing Identity">
                            </a>
                        </div>
                    @endif

                    <form wire:submit="edit">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
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

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="identity" class="form-label">Identitas</label>
                                    <input type="file"
                                        class="form-control bg-white @error('identity') is-invalid @enderror"
                                        wire:model="identity" id="identity" aria-describedby="identityId"
                                        placeholder="Enter user identity" accept="image/*" />
                                    @error('identity')
                                        <small id="identityId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                        wire:model="dob" id="dob" aria-describedby="dobId"
                                        placeholder="Enter user dob" />
                                    @error('dob')
                                        <small id="dobId" class="form-text text-danger">{{ $message }}</small>
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
