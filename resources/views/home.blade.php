<?php

use App\Models\Booking;
use App\Models\BookingTime;
use App\Models\PaymentRecord;
use function Livewire\Volt\{state, usesPagination, computed};
use Carbon\Carbon;

usesPagination(theme: "bootstrap");

state([
    "totalBookings" => Booking::count() ?: 0,
    "totalUnpaidBookings" => Booking::where("status", "UNPAID")->count() ?: 0,
    "totalCompletedBookings" => BookingTime::where("status", "STOP")->count() ?: 0,
    "totalConfirmedPayments" => PaymentRecord::where("status", "PAID")->sum("gross_amount") ?: 0,
    "totalPendingPayments" => PaymentRecord::where("status", "DRAF")->sum("gross_amount") ?: 0,
    // 'verificationBookings' => Booking::where('status', 'VERIFICATION')->orWhere('status', 'PROCESS')->paginate(10),
]);

$verificationBookings = computed(function () {
    return Booking::whereDate("created_at", Carbon::today())->paginate(10);
});

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route("home") }}">Dashboard</a></li>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @volt
        <div class="conteiner-fluid">
            <!-- Grafik Booking -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Data dari backend (menggunakan json_encode agar data diparsing dengan benar)
                    const totalBookings = {{ json_encode($totalBookings) }};
                    const totalUnpaidBookings = {{ json_encode($totalUnpaidBookings) }};
                    const totalCompletedBookings = {{ json_encode($totalCompletedBookings) }};

                    const totalConfirmedPayments = {{ json_encode($totalConfirmedPayments) }};
                    const totalPendingPayments = {{ json_encode($totalPendingPayments) }};

                    // Chart Booking
                    new Chart(document.getElementById('bookingChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Total Booking', 'Belum Dibayar', 'Selesai'],
                            datasets: [{
                                label: 'Jumlah Booking',
                                data: [totalBookings, totalUnpaidBookings, totalCompletedBookings],
                                backgroundColor: ['#007bff', '#ffc107', '#28a745'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Chart Pembayaran
                    new Chart(document.getElementById('paymentChart').getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: ['Dikonfirmasi', 'Pending'],
                            datasets: [{
                                data: [totalConfirmedPayments, totalPendingPayments],
                                backgroundColor: ['#28a745', '#ffc107'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });
                });
            </script>
            <div class="row gap-2 px-3">
                <div class="col-md-8 card mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold text-center">Statistik Booking</h5>
                        <canvas id="bookingChart"></canvas>
                    </div>
                </div>

                <!-- Grafik Pembayaran -->
                <div class="col-md card mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold text-center">Statistik Pembayaran</h5>
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card" wire:poll.15s>
                <h6 class="card-header fw-bold text-center">
                    Tabel Penyewaan Baru
                    </h5>
                    <div class="card-body">
                        <div class="table-responsive border rounded">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Pelanggan</th>
                                        <th>Invoice</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->verificationBookings as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->invoice }}</td>
                                            <td>{{ __("booking." . $item->status) }}</td>
                                            <td>{{ formatRupiah($item->total_price) }}</td>
                                            <td>
                                                <div>
                                                    <a href="{{ route("transactions.show", ["booking" => $item->id]) }}"
                                                        class="btn btn-sm btn-primary">Detail</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $this->verificationBookings->links() }}
                        </div>

                    </div>
            </div>

        </div>
    @endvolt
</x-admin-layout>
