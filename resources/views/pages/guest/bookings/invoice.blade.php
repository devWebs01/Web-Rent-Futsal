<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses, rules};

uses([LivewireAlert::class]);

state([
    'user' => fn() => $this->booking->user,
    'totalPrice' => fn() => $this->booking->times->sum('price'),
    'payment' => fn() => $this->booking->payment,
    'booking',
]);
?>


@volt
    <div>
        <!-- Invoice 1 - Bootstrap Brain Component -->
        <section class="py-3 py-md-5">
            <div class="row mb-4">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-dark mb-3 d-print-none" id="printInvoiceBtn">Download
                        Invoice</button>
                </div>
            </div>

            <script>
                document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                    window.print(); // Fungsi bawaan browser untuk mencetak halaman
                });
            </script>

            <div class="row gy-3 mb-3">
                <div class="col-6">
                    <h4 class="text-uppercase text-danger m-0">Invoice</h4>
                </div>
                <div class="col-6">
                    <h4 class="text-uppercase text-danger text-end m-0">{{ $booking->invoice }}</h4>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-sm-6 col-md-8">
                    <small class="text-muted">Pemesanan</small>
                    <address>
                        <strong>
                            {{ $user->name }} <br>
                            {{ __('status.' . $booking->status) }} <br>
                            {{ $booking->created_at }}
                        </strong>
                    </address>
                </div>
                <div class="col-12 col-sm-6 col-md-4 text-end">
                    <small class="text-muted">
                        Pembayaran
                    </small>
                    <address>
                        <strong>
                            Metode : {{ __('status.' . $booking->payment_method) }} <br>
                            Telp. Alternatif : {{ $booking->alternative_phone }} <br>
                        </strong>

                    </address>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-uppercase">Lapangan</th>
                                    <th scope="col" class="text-uppercase">Jam</th>
                                    <th scope="col" class="text-uppercase text-end">Type</th>
                                    <th scope="col" class="text-uppercase text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @foreach ($booking->times as $time)
                                    <tr>
                                        <th>{{ $time->field->field_name }}</th>
                                        <td>{{ $time->start_time . ' - ' . $time->end_time }}</td>
                                        <td class="text-end">
                                            {{ __('type.' . $time->type) }}
                                        </td>
                                        <td class="text-end">
                                            {{ formatRupiah($time->price) }}
                                        </td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th scope="row" colspan="3" class="text-uppercase text-end">Total</th>
                                    <td class="text-end">
                                        {{ formatRupiah($totalPrice) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($payment->records as $item)
                    <div class="col-md">
                        <div class="card text-start">
                            @if ($item->receipt)
                                <img class="card-img-top" src="{{ Storage::url($item->receipt) }}" alt="Title" />
                            @else
                                <div class="card placeholder" style="height: 250px; width: 100%">
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        Status
                                    </div>
                                    <div class="col-6 text-end">
                                        {{ __('status.' . $item->status) }}
                                    </div>
                                    <div class="col-6">
                                        Jumlah
                                    </div>
                                    <div class="col-6 text-end">
                                        {{ formatRupiah($item->amount) }}
                                    </div>
                                    <div class="col-6">
                                        Dibayar pada
                                    </div>
                                    <div class="col-6 text-end">
                                        {{ $item->updated_at }}
                                    </div>
                                </div>
                                @if (empty($item->receipt))
                                    <a class="btn btn-dark d-grid"
                                        href="{{ route('payment_record.show', ['paymentRecord' => $item->id]) }}"
                                        role="button">Input Pembayaran</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </section>
    </div>
@endvolt
