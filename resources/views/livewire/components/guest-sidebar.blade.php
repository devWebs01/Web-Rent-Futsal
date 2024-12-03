<?php

use App\Models\Cart;
use function Livewire\Volt\{state, uses, on};
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

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

?>

@volt
    <div>
        <div class="offcanvas-header row">
            <div class="col-4">
                <button type="button" class="btn-close btn-close-black m-3" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="col-8 fw-bold">
                JADWAL DIPILIH
            </div>
            <div class="d-grid col-12">
                <button {{ $cart->count() > 0 ?: 'disabled' }} type="button" class="btn btn-outline-dark">
                    BOOKING
                </button>
            </div>
        </div>
        <div class="offcanvas-body">
            <div class="flex-grow-1 text-dark">
                @foreach ($cart as $item)
                    <div class="row mb-3 py-3 border-5 border-start border-black rounded-3 bg-yellow">
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
@endvolt
