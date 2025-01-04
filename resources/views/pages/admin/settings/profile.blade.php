<?php

use App\Models\Setting;
use function Livewire\Volt\{state, computed, rules, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

$getSetting = computed(function () {
    return Setting::first();
});

state([
    'name' => fn() => $this->getSetting->name ?? '',
    'telp' => fn() => $this->getSetting->telp ?? '',
    'whatsApp' => fn() => $this->getSetting->whatsApp ?? '',
    'address' => fn() => $this->getSetting->address ?? '',
]);

rules([
    'name' => 'required|min:5',
    'telp' => 'required|numeric',
    'whatsApp' => 'required|numeric',
    'address' => 'required|min:5',
]);

$save = function () {
    $validate = $this->validate();

    // Menggunakan updateOrCreate untuk memperbarui atau membuat Setting
    Setting::updateOrCreate(
        ['id' => $this->getSetting ? Setting::first()->id : null], // Kondisi pencarian
        $validate, // Data yang akan diperbarui atau dibuat
    );

    $this->alert('success', 'Data berhasil diupdate!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);
};

?>
@volt
    <div>
        <div class="alert alert-primary mt-3" role="alert">
            <i class='bx bxs-bell-ring pr-2' ></i> <strong>Pemberitahuan Penting</strong>
            <p>
                Sebagai admin, Anda memiliki akses penuh untuk mengelola pengguna
                dan konten. Harap pastikan bahwa semua tindakan yang Anda lakukan sesuai dengan kebijakan dan prosedur yang
                berlaku. Jika Anda menemukan masalah atau memerlukan bantuan, silakan hubungi tim dukungan.
            </p>
        </div>

        <form wire:submit="save">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" id="name"
                    aria-describedby="nameId" placeholder="Enter name" />
                @error('name')
                    <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md">
                    <div class="mb-3">
                        <label for="telp" class="form-label">Telp</label>
                        <input type="number" class="form-control @error('telp') is-invalid @enderror" wire:model="telp"
                            id="telp" aria-describedby="telpId" placeholder="Enter telp" />
                        @error('telp')
                            <small id="telpId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md">
                    <div class="mb-3">
                        <label for="whatsApp" class="form-label">WhatsApp</label>
                        <input type="number" class="form-control @error('whatsApp') is-invalid @enderror"
                            wire:model="whatsApp" id="whatsApp" aria-describedby="whatsAppId"
                            placeholder="Enter whatsApp" />
                        @error('whatsApp')
                            <small id="whatsAppId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>



            <div class="mb-3">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address" id="address"
                    aria-describedby="addressId" placeholder="Enter Alamat Lengkap" rows="8"></textarea>

                @error('address')
                    <small id="addressId" class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="row mb-3">
                <div class="col-md">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
                <div class="col-md align-self-center text-end">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                </div>
            </div>
        </form>
    </div>
@endvolt
