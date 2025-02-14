<?php

use App\Models\Booking;
use App\Models\BookingTime;
use App\Models\PaymentRecord;
use Livewire\Component;
use function Livewire\Volt\{state, computed};

state([
    'totalBookings' => Booking::count() ?: 0,
    'totalUnpaidBookings' => Booking::where('status', 'UNPAID')->count() ?: 0,
    'totalCompletedBookings' => BookingTime::where('status', 'STOP')->count() ?: 0,
    'totalConfirmedPayments' => PaymentRecord::where('status', 'PAID')->sum('gross_amount') ?: 0,
    'totalPendingPayments' => PaymentRecord::where('status', 'DRAF')->sum('gross_amount') ?: 0,
]);

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    @volt
        <div class="container-fluid">
            <!-- Grafik Booking -->

            <div class="row gap-2">
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
        </div>

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
    @endvolt
</x-admin-layout>
