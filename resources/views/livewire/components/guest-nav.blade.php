<?php

use App\Models\Cart;
use function Livewire\Volt\{state, computed, on};


state([
    'cart' => fn() => Cart::where('user_id', auth()->user()->id ?? null)->get(),
    'subTotal' => fn() => Cart::where('user_id', auth()->user()->id ?? null)
        ->get()
        ->sum('price'),
]);
on([
    'cart-updated' => function () {
        $this->cart = Cart::where('user_id', auth()->user()->id ?? null)->get();
        $this->subTotal = Cart::where('user_id', auth()->user()->id ?? null)
            ->get()
            ->sum('price');
    },
]);

?>

@volt
    <div>
        <ul class="navbar-nav flex-grow-1 p-4">
            <li class="nav-item">
                <a class="nav-link active text-uppercase ls-4 text-white" aria-current="page" href="/">Home</a>
            </li>
            <li class="nav-item position-relative">
                <a class="nav-link text-uppercase ls-4 text-white" aria-current="page" href="/">
                    Keranjang
                </a>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    99+
                </span>
            </li>
            </li>
        </ul>

    </div>
@endvolt
