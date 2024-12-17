<?php

use App\Models\PaymentRecord;
use App\Models\Bank;
use function Livewire\Volt\{state, uses, usesFileUploads};
use function Laravel\Folio\{name};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

usesFileUploads();

name('payment_record.show');

state([
    'booking' => fn() => $this->paymentRecord->payment->booking,
    'payment' => fn() => $this->paymentRecord->payment,
    'banks' => fn() => Bank::get(),
    'receipt',
    'paymentRecord',
]);

$submit = function () {
    // Validasi input
    $this->validate([
        'receipt' => 'required|image|mimes:jpeg,png,jpg',
    ]);

    // Update payment
    $payment = PaymentRecord::findOrFail($this->paymentRecord->id);
    $payment->update([
        'receipt' => $this->receipt->store('public/receipt'),
        'payment_status' => 'WAITING', // Ubah status pembayaran menjadi WAITING_CONFIRM_PAYMENT
    ]);

    $this->booking->update([
        'status' => 'PROCESS',
    ]);

    // Set alert untuk notifikasi
    $this->alert('success', 'Bukti pembayaran berhasil diunggah!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
        'width' => 500,
    ]);

    // Redirect atau lakukan tindakan lainnya
    $this->redirectRoute('bookings.show', ['booking' => $this->booking->id]);
};

?>

<x-guest-layout>
    <x-slot name="title">Detail Pambayaran</x-slot>
    @volt
        <div>
            <section class="container">
                <span class="fw-bold">Invoice</span>
                <h4 class="display-6 fw-bold text-danger">
                    {{ $booking->invoice }}
                </h4>
                <p class="text-muted">
                    Anda telah sampai di thap akhir proses pemesanan, pastikan semua detail anda sudah benar dan inputkan
                    bukti pembayaran. Silahkan lalkukan pembayaran melalui salah satu rekening berikut:
                </p>
            </section>

            <section>
                <div class="container">
                    <div class="card">
                        <div class="d-flex">
                            @foreach ($banks as $item)
                                <div class="card-body">
                                    <div class="alert alert-light" role="alert">
                                        <h4 class="text-danger">{{ $item->bank_name }}</h4>

                                        <h6>{{ $item->account_owner }}</h6>
                                        <h6>{{ $item->account_number }}</h6>

                                    </div>


                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="container">
                    <div class="row my-3">

                        <div class="col">
                            <h2 id="font-custom" style="color: #f35525">
                                {{ $payment->payment_type }}
                            </h2>
                            <h6>Total Pembayaran:</h6>
                            <div class="row">
                                <div class="col-lg-7">
                                    <h1 id="font-custom">
                                        {{ formatRupiah($paymentRecord->amount) }}
                                    </h1>

                                    <div class="my-3">
                                        <form wire:submit="submit">
                                            @csrf
                                            <div class="mb-3">
                                                <p class="form-label fw-semibold">Status:
                                                    {{ __('status.' . $paymentRecord->status) }}</p>
                                                <p class="form-label fw-semibold">Tanggal Pembayaran:
                                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d F Y') }}</p>

                                                <label for="receipt" class="form-label fw-semibold">
                                                    Silahkan masukkan bukti pembayaran anda!
                                                    <div wire:loading class="spinner-border spinner-border-sm ms-2"
                                                        role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </label>
                                                <input type="file" class="form-control" accept="image/*"
                                                    wire:model='receipt'>
                                                @error('receipt')
                                                    <p id="receipt" class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="d-grid">
                                                <button class="btn btn-outline-secondary" type="submit">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col">
                                    @if ($receipt)
                                        <div class="card">
                                            <a data-fslightbox="mygalley" class="rounded-4" target="_blank"
                                                data-type="image" href="{{ $receipt->temporaryUrl() }}">
                                                <img src="{{ $receipt->temporaryUrl() }}" class="img object-fit-cover"
                                                    style="height: 250px; width: 100%" alt="receipt" />
                                            </a>
                                        </div>
                                    @else
                                        <div class="card placeholder" style="height: 250px; width: 100%">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endvolt
</x-guest-layout>
