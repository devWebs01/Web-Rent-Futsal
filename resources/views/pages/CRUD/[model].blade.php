<?php

use App\Models\model;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('models.edit');

state([
    'model',
    // isi
]);

rules([
    // isi
]);

$edit = function () {
    $model = $this->model;

    $validateData = $this->validate();

    $model->update($validateData);

    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('models.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Edit model</x-slot>


    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit model</strong>
                        <p>Pada halaman edit model, kamu dapat mengubah informasi model yang sudah ada.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="contoh1" class="form-label">contoh1</label>
                                    <input type="text" class="form-control @error('contoh1') is-invalid @enderror"
                                        wire:model="contoh1" id="contoh1" aria-describedby="contoh1Id"
                                        placeholder="Enter model contoh1" autofocus autocomplete="contoh1" />
                                    @error('contoh1')
                                        <small id="contoh1Id" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="contoh2" class="form-label">contoh2</label>
                                    <input type="text" class="form-control @error('contoh2') is-invalid @enderror"
                                        wire:model="contoh2" id="contoh2" aria-describedby="contoh2Id"
                                        placeholder="Enter model contoh2" />
                                    @error('contoh2')
                                        <small id="contoh2Id" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
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
            </div>
        </div>
    @endvolt
</x-admin-layout>
