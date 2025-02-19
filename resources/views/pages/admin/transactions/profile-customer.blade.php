<?php

use function Livewire\Volt\{state, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Carbon\Carbon;

uses([LivewireAlert::class]);


state([
    'customer' => fn() => $this->booking->user,
    'name' => fn() => $this->customer->name ?? '',
    'email' => fn() => $this->customer->email ?? '',
    'phone' => fn() => $this->customer->phone ?? '',
    'identity' => fn() => $this->customer->identity,
    'dob' => fn() => $this->customer->identity->dob ?? '',
    'booking',
]);

$rejectIdentity = function () {
    try {
        $this->booking->update([
            'status' => 'CANCEL',
        ]);

        $this->alert('success', 'Proses berhasil!', [
            'position' => 'center',
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagal!', [
            'position' => 'center',
        ]);
    }
};

$confirmIdentity = function () {
    try {
        $this->booking->update([
            'status' => 'UNPAID',
            'expired_at' => Carbon::now()->addMinutes(10)
        ]);

        $this->alert('success', 'Proses berhasil!', [
            'position' => 'center',
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagal!', [
            'position' => 'center',
        ]);
    }
};

?>

<div>
    @volt
        <div>
            <div class="card-img-top mb-5 rounded">
                <a href="{{ Storage::url($customer->identity->document) }}" data-fancybox data-caption="Identitas customer">
                    <img src="{{ Storage::url($customer->identity->document) }}" class="img" style="object-fit: cover;"
                        width="100%" height="200px" alt="Existing Identity">
                </a>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                            id="name" aria-describedby="nameId" placeholder="Enter user name" autofocus
                            autocomplete="name" readonly />
                        @error('name')
                            <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email"
                            id="email" aria-describedby="emailId" placeholder="Enter user email" readonly />
                        @error('email')
                            <small id="emailId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telepon</label>
                        <input type="number" class="form-control @error('phone') is-invalid @enderror" wire:model="phone"
                            id="phone" aria-describedby="phoneId" placeholder="Enter user phone" readonly />
                        @error('phone')
                            <small id="phoneId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                @if (!empty($identity))
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dob" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control @error('dob') is-invalid @enderror" wire:model="dob"
                                id="dob" aria-describedby="dobId" placeholder="Enter user dob" readonly />
                            @error('dob')
                                <small id="dobId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                @endif

                @if ($booking->status === 'VERIFICATION')    
                <div class="d-flex gap-6 my-5 justify-content-between" >
                    <button wire:click='rejectIdentity'
                    wire:confirm="Yakin tolak penyawaan ini? Status pemesanan akan diupdate menjadi BATAL jika memilih ini!"
                    type="button" class="btn btn-danger w-100">
                        Tolak
                    </button>
                    <button type="button" class="btn btn-success w-100">
                        Konfirmasi
                    </button>
                </div>
                @endif

            </div>

        </div>
    @endvolt
</div>
