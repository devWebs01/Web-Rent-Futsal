<?php

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Booking;
use App\Models\BookingTime;
use function Livewire\Volt\{state, uses, on};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

state([
    'cart' => fn() => Cart::where('user_id', auth()->user()->id ?? '')->get(),
    'subTotal' => fn() => Cart::where('user_id', auth()->user()->id ?? '')->sum('price'),
    'userId' => Auth()->user()->id ?? '',
]);

on([
    'cart-updated' => function () {
        $this->cart = Cart::where('user_id', auth()->user()->id)->get();
        $this->subTotal = Cart::where('user_id', auth()->user()->id)->sum('price');
    },
]);

$destroy = function (Cart $cart) {
    try {
        $cart->delete();
        $this->alert('success', 'Jadwal berhasil dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->dispatch('cart-updated');
    } catch (\Throwable $th) {
        $this->alert('error', 'Jadwal gagal dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

$processBooking = function () {
    $userId = $this->userId;

    DB::transaction(function () use ($userId) {
        // Ambil data keranjang milik user
        $carts = Cart::where('user_id', $userId)->get();

        if ($carts->isEmpty()) {
            $this->alert('error', 'Keranjang kosong, tidak dapat memproses booking!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
        }

        // Hitung total harga
        $totalPrice = $carts->sum('price');

        // Generate invoice unik
        $invoice = 'INV-' . strtoupper(uniqid());

        // Buat record di tabel bookings
        $booking = Booking::create([
            'invoice' => $invoice,
            'user_id' => $userId,
            'status' => 'UNPAID', // Status default
            'total_price' => $totalPrice,
        ]);

        // Loop data dari keranjang untuk disimpan di tabel booking_times
        foreach ($carts as $cart) {
            // dd($cart);
            BookingTime::create([
                'booking_id' => $booking->id,
                'field_id' => $cart->field_id,
                'booking_date' => $cart->booking_date,
                'start_time' => $cart->start_time,
                'end_time' => $cart->end_time,
                'type' => $cart->type,
                'price' => $cart->price,
            ]);
        }

        $this->alert('success', 'Keranjang kosong, tidak dapat memproses booking!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->redirectRoute('bookings.show', ['booking' => $booking]);

        // Hapus data dari keranjang setelah diproses
        Cart::where('user_id', $userId)->delete();
    });
};

?>

@volt
    <div>
        <button class="navbar-toggler border-0 position-relative" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <span class="navabar-toggler-icon">
                <i class='bx bx-bookmarks bx-border-circle fs-2'></i>
            </span>
            @if ($cart->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $cart->count() }}
                </span>
            @endif
        </button>
        <div class="offcanvas offcanvas-end bg-white" tabindex="-1" id="offcanvasNavbar"
            aria-labelledby="offcanvasNavbarLabel">

            <div class="offcanvas-header row">
                <div class="col-4">
                    <button type="button" class="btn-close btn-close-black m-3" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="col-8 fw-bold">
                    JADWAL DIPILIH
                </div>
                <div class="d-grid col-12">
                    <button @disabled($cart->isEmpty()) type="button" class="btn btn-outline-dark"
                        wire:click='processBooking'>
                        BOOKING
                    </button>
                </div>
            </div>
            <div class="offcanvas-body">
                <div class="flex-grow-1 text-dark">
                    @foreach ($cart as $item)
                        <div wire:poll.15s class="row mb-3 py-3 border-5 border-start border-black rounded-3 bg-yellow">
                            <div class="col-10">
                                <p class="fw-bold mb-0">
                                    {{ $item->field->field_name }}
                                    <span class="badge bg-primary">
                                        {{ __('type.' . $item->type) }}
                                    </span>
                                </p>
                                <small class="fw-bold postf mb-0">
                                    <span class="text-danger">
                                        {{ Carbon::parse($item->booking_date)->format('d M Y') }}
                                    </span>
                                    ,
                                    <span>{{ $item->start_time . '-' . $item->end_time }}</span>
                                </small>
                                <br>
                                <small class="fw-bold postf">
                                    {{ formatRupiah($item->price) }}
                                </small>
                            </div>
                            <div class="col-2 align-content-center">
                                <button class="btn border-0 text-danger" wire:click='destroy({{ $item->id }})'
                                    wire:loading.attr='disable'>
                                    <i class='bx bxs-trash bx-sm'></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endvolt
