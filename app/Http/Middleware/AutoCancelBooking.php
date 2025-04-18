<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoCancelBooking
{
    public function handle(Request $request, Closure $next)
    {
        $expire_time = 10; // menit

        $expiredBookings = Booking::where('status', 'UNPAID')
            ->where('created_at', '<=', now()->subMinutes($expire_time))
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'CANCEL']);

            // Pastikan relasi payment dan records memang ada
            if ($booking->payment && $booking->payment->records) {
                foreach ($booking->payment->records as $record) {
                    $record->update(['status' => 'FAILED']);
                }
            }
        }

        return $next($request);
    }
}
