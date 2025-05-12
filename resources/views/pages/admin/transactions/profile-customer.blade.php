<?php

use Carbon\Carbon;
use App\Mail\ConfirmPaymentMail;
use App\Mail\RejectBookingMail;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses};

uses([LivewireAlert::class]);

state([
    "customer" => fn() => $this->booking->user,
    "name" => fn() => $this->customer->name ?? "",
    "email" => fn() => $this->customer->email ?? "",
    "phone" => fn() => $this->customer->phone ?? "",
    "identity" => fn() => $this->customer->identity,
    "dob" => fn() => $this->customer->identity->dob ?? "",
    "requires_identity_validation" => fn() => $this->booking->bookingTimes->contains(fn($item) => $item->type === "STUDENT"),

    "booking",
]);

$rejectIdentity = function () {
    try {
        // Kirim Email Penolakan
        Mail::to($this->booking->user->email)->send(new RejectBookingMail($this->booking));

        $this->booking->update([
            "status" => "CANCEL",
        ]);

        $this->booking->payment
            ->records()
            ->where("status", "DRAF")
            ->update([
                "status" => "FAILED",
                "status_message" => "Identitas ditolak, pembayaran dibatalkan",
            ]);

        $this->alert("success", "Proses berhasil!", [
            "position" => "center",
        ]);

        $this->redirectRoute("transactions.show", ["booking" => $this->booking->id]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal!", [
            "position" => "center",
        ]);
    }
};

$confirmIdentity = function () {
    try {
        // Kirim Email Konfirmasi Pembayaran
        Mail::to($this->booking->user->email)->send(new ConfirmPaymentMail($this->booking));

        $this->booking->update([
            "status" => "UNPAID",
            "expired_at" => Carbon::now()->addMinutes(10),
        ]);

        $this->booking->payment->records()->update([
            "status" => "UNPAID",
            "status_message" => "Silahkan melakukan pembayaran",
        ]);

        $this->alert("success", "Proses berhasil!", [
            "position" => "center",
        ]);

        $this->redirectRoute("transactions.show", ["booking" => $this->booking->id]);
    } catch (\Throwable $th) {
        Log::error("Error saat konfirmasi identitas: " . $th->getMessage());

        $this->alert("error", "Proses gagal!", [
            "position" => "center",
        ]);
    }
};

?>

<div>
    @volt
        <div>
            <h4 class="h5 fw-bold mb-4">
                Data Pelanggan
            </h4>

            @if ($requires_identity_validation)
                @if (!empty($identity))
                    <div class="card-img-top my-5 rounded">
                        <a href="{{ Storage::url($customer->identity->document) }}" data-fancybox
                            data-caption="Identitas customer">
                            <img src="{{ Storage::url($customer->identity->document) }}" class="img border"
                                style="object-fit: cover;" width="100%" height="200px" alt="Existing Identity">
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">
                        <strong>Perhatian:</strong> Customer belum mengunggah dokumen identitas.
                    </div>
                @endif
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error("name") is-invalid @enderror" wire:model="name"
                            id="name" aria-describedby="nameId" placeholder="Enter user name" autofocus
                            autocomplete="name" readonly />
                        @error("name")
                            <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error("email") is-invalid @enderror" wire:model="email"
                            id="email" aria-describedby="emailId" placeholder="Enter user email" readonly />
                        @error("email")
                            <small id="emailId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telepon</label>
                        <input type="number" class="form-control @error("phone") is-invalid @enderror" wire:model="phone"
                            id="phone" aria-describedby="phoneId" placeholder="Enter user phone" readonly />
                        @error("phone")
                            <small id="phoneId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                @if (!empty($identity))
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="dob" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control @error("dob") is-invalid @enderror" wire:model="dob"
                                id="dob" aria-describedby="dobId" placeholder="Enter user dob" readonly />
                            @error("dob")
                                <small id="dobId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                @endif

                @if ($booking->status === "VERIFICATION")
                    <div class="d-flex gap-6 my-5 justify-content-between">
                        <button wire:click='rejectIdentity'
                            wire:confirm="Yakin tolak penyawaan ini? Status pemesanan akan diupdate menjadi BATAL jika memilih ini!"
                            type="button" class="btn btn-danger w-100">
                            Tolak
                        </button>
                        <button type="button" wire:click='confirmIdentity' class="btn btn-success w-100">
                            Konfirmasi
                        </button>
                    </div>
                @endif

                <div wire:loading wire:target='confirmIdentity, rejectIdentity' class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="spinner-grow spinner-grow-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

            </div>

        </div>
    @endvolt
</div>
