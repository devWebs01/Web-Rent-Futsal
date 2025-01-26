<?php

use App\Models\Setting;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Booking;
use App\Models\BookingTime;
use function Livewire\Volt\{state, uses, on};
use Jantinnerezo\LivewireAlert\LivewireAlert;
uses([LivewireAlert::class]);


$logout = function () {
    Auth::logout();
    $this->redirect('/');
};

state([
    'cart' => fn() => Cart::where('user_id', auth()->user()->id ?? '')->get(),
    'subTotal' => fn() => Cart::where('user_id', auth()->user()->id ?? '')->sum('price'),
    'userId' => Auth()->user()->id ?? '',
    'setting' => fn() => Setting::first()
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
            return;
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

        foreach ($carts as $cart) {
            // Validasi slot waktu
            $conflictingTime = BookingTime::where('field_id', $cart->field_id)
                ->where('booking_date', $cart->booking_date)
                ->where('type', $cart->type) // Validasi tipe booking
                ->where('status', '!=', 'CANCEL') // Abaikan slot yang sudah dibatalkan
                ->where(function ($query) use ($cart) {
                    $query->where(function ($subQuery) use ($cart) {
                        // Cek apakah waktu mulai berada di dalam interval booking yang ada
                        $subQuery->where('start_time', '<', $cart->end_time)->where('end_time', '>', $cart->start_time);
                    });
                })
                ->lockForUpdate()
                ->first(); // Ambil data konflik pertama

            if ($conflictingTime) {
                $this->alert('error', 'Waktu yang dipilih sudah dipesan untuk lapangan dan tipe yang sama! ' . '<br> <br>' . ' Waktu yang sudah dibooking: ' . $conflictingTime->start_time . ' - ' . $conflictingTime->end_time, [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => true,
                ]);
                return;
            }

            // Simpan slot waktu ke tabel booking_times
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

        // Hapus data dari keranjang setelah diproses
        Cart::where('user_id', $userId)->delete();

        $this->alert('success', 'Booking berhasil diproses!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->redirectRoute('bookings.show', ['booking' => $booking]);
    });
};

?>

@volt
<div>
    <nav class="fixed-top navbar navbar-expand-lg navbar-light bg-none">
        <div class="container-fluid mx-3 border rounded bg-light">
            <!-- Toggler Menu Utama -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Logo -->
            <a class="navbar-brand fw-bolder" href="/">
                {{ $setting->name }}
            </a>

            <!-- Menu Utama -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-bolder {{ Request::is('/') ? 'active text-primary' : '' }}"
                            href="/">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bolder {{ Route::is('catalogs.field') ? 'active text-primary' : '' }}"
                            href="/#fields">Lapangan</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link fw-bolder {{ Route::is('bookings.index') ? 'active text-primary' : '' }}"
                                href="{{ route('bookings.index') }}">Pesanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bolder {{ Route::is('profile.guest') ? 'active text-primary' : '' }}"
                                href="{{ route('profile.guest') }}">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bolder" wire:click='logout'>Keluar</a>
                        </li>
                    @endauth
                </ul>
            </div>

            @guest
                <a href="{{ route('login') }}" class="btn btn-primary">Masuk Akun</a>
            @else
                @if (Auth()->user()->role === 'admin')
                    <a href="{{ route('home') }}" class="btn btn-primary">Halaman Admin</a>
                @else
                    <!-- Tombol Offcanvas Menu Tambahan -->
                    <button class="btn border-0 position-relative" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                        <span class="navabar-toggler-icon">
                            <i class='bx bx-shopping-bag fs-2'></i>
                        </span>
                        @if ($cart->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                {{ $cart->count() }}
                            </span>
                        @endif
                    </button>
                    <div class="offcanvas offcanvas-end bg-white" tabindex="-1" id="offcanvasNavbar"
                        aria-labelledby="offcanvasNavbarLabel">
                        <!-- Offcanvas Content -->
                    </div>
                @endif
            @endguest
        </div>
    </nav>

</div>

@endvolt
