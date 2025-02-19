<?php

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\PaymentRecord;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, uses, rules};

uses([LivewireAlert::class]);

state([
    'user' => fn() => $this->booking->user,
    'totalPrice' => fn() => $this->booking->bookingtimes->sum('price'),
    'payment' => fn() => $this->booking->payment,
    'expired_at' => fn() => $this->booking->expired_at ?? '',
    'fullpayment' => fn() => $this->booking->total_price,
    'downpayment' => fn() => $this->booking->total_price / 2,
    'booking',
]);

$getTimeRemainingAttribute = function () {
    $now = Carbon::now();
    $expiry = Carbon::parse($this->expired_at);

    if ($expiry->isPast()) {
        return 'Expired';
    }

    $diffInSeconds = $expiry->diffInSeconds($now);
    $minutes = floor($diffInSeconds / 60);
    $seconds = $diffInSeconds % 60;

    return "{$minutes}m {$seconds}s";
};

$processPayment = function ($id) {
    $record = PaymentRecord::find($id);

    if (!empty($record->snapToken)) {
        $this->redirectRoute('payment_record.show', [
            'paymentRecord' => $id,
        ]);
    } else {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Data transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $record->order_id,
                'gross_amount' => $this->booking->payment_method === 'fullpayment' ? $this->fullpayment : $this->downpayment,
            ],
            'customer_details' => [
                'first_name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->telp,
            ],
            'expiry' => [
                'start_time' => $this->booking->expired_at ? Carbon::parse($this->booking->expired_at)->format('Y-m-d H:i:s O') : Carbon::now()->format('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => $this->booking->expired_at ? Carbon::now()->diffInMinutes(Carbon::parse($this->booking->expired_at)) : 5, // Menghitung durasi kedaluwarsa dalam menit
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $record->update(['snapToken' => $snapToken]);

            $this->redirectRoute('payment_record.show', [
                'paymentRecord' => $id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment Error: ' . $e->getMessage());

            $this->alert('error', 'Ada yang salah pada input data! Payment Error: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }
};

?>


@volt
    @push('scripts')
        <script>
            document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                window.print(); // Fungsi bawaan browser untuk mencetak halaman
            });
        </script>
    @endpush

    <div>
        <div class="alert alert-danger text-center {{ $booking->status === 'UNPAID' ?: 'd-none' }}" role="alert">
            Selesaikan proses penyewaan lapangan dalam
            <strong @if (now()->lessThan(\Carbon\Carbon::parse($expired_at)) && $booking->status === 'UNPAID') wire:poll.1s @endif>
                {{ $this->getTimeRemainingAttribute() }}
            </strong>
        </div>

        <div class="card">
            <div class="card-body">

                <!-- Invoice 1 - Bootstrap Brain Component -->
                <section>
                    <div class="row mb-4">
                        <div class="col-6">
                            <button class="btn btn-primary btn-lg text-uppercase">
                                {{ __('booking.' . $booking->status) }}
                            </button>

                        </div>
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-dark btn-lg mb-3 d-print-none"
                                id="printInvoiceBtn">Download
                                Invoice</button>
                        </div>
                    </div>

                    <div class="row gy-3 mb-3">
                        <div class="col-6">
                            <h4 class="text-uppercase text-primary m-0">Invoice</h4>
                        </div>
                        <div class="col-6">
                            <h4 class="text-uppercase text-primary text-end m-0">{{ $booking->invoice }}</h4>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <small class="h5 fw-bold">Penyewaan</small>
                        <div class="col-12 col-sm-6 col-md-8">
                            <address>
                                <div>{{ $user->name }}</div>
                                <div>{{ $user->email }}</div>
                                <div>{{ $user->phone }}</div>
                            </address>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 text-end">
                            <address>
                                <div>{{ $booking->created_at->format('d m Y h:i:s') }}</div>
                                <div>
                                    Metode Pembayaran :
                                    {{ __('status.' . $booking->payment_method) }}
                                </div>
                                <div>
                                    No. Telp Alternatif :
                                    {{ $booking->alternative_phone ?? '-' }}
                                </div>

                            </address>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-uppercase">Lapangan</th>
                                            <th scope="col" class="text-uppercase">Hari</th>
                                            <th scope="col" class="text-uppercase">Jam</th>
                                            <th scope="col" class="text-uppercase text-end">Type</th>
                                            <th scope="col" class="text-uppercase text-end">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        @foreach ($booking->bookingTimes as $time)
                                            <tr>
                                                <th>{{ $time->field->field_name }}</th>
                                                <th>{{ Carbon::parse($time->booking_date)->format('d-m-Y') }}</th>
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
                                            <th scope="row" colspan="4" class="text-uppercase text-end">Total</th>
                                            <td class="text-end">
                                                {{ formatRupiah($totalPrice) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary d-print-none" role="alert">
                        <strong>Pemberitahuan Pembayaran</strong><br>
                        Setelah pembayaran DP, Anda dapat melakukan pembayaran akhir di tempat/melunasi nya langsung dengan
                        sistem pembayaran yang tersedia. Untuk pembayaran penuh,
                        harap segera diselesaikan.
                    </div>

                    <div class="row">
                        @foreach ($payment->records as $item)
                            <div class="col-md mb-3">
                                <div class="card text-start mb-3 h-100">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                Status
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ __('record.' . $item->status) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah harus dibayar
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $booking->payment_method === 'fullpayment' ? formatRupiah($fullpayment) : formatRupiah($downpayment) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah yang diterima
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ formatRupiah($item->gross_amount) ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Waktu pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $item->payment_time ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Jenis pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $item->payment_type ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Detail
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $item->payment_detail ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Info
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $item->status_message ?? '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if ($booking->status !== 'CANCEL' && $booking->status !== 'VERIFICATION')
                                        <div class="card-footer bg-white border-0">
                                            <button type="button" wire:click='processPayment({{ $item->id }})'
                                                class="btn btn-dark w-100 mb-3 {{ $item->status === 'DRAF' ?: 'd-none' }}"
                                                role="button">
                                                <span>Lakukan Pembayaran</span>
                                                <div wire:loading wire:target='processPayment'
                                                    class="spinner-border spinner-border-sm ms-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </section>
            </div>
        </div>
    </div>
@endvolt
