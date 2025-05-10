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
            ->where('expired_at', '<=', now()->subMinutes($expire_time))
            ->get();

        // Jika tidak ada booking yang kadaluarsa, lanjutkan saja tanpa proses
        if ($expiredBookings->isEmpty()) {
            return $next($request);
        }

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'CANCEL']);

            if ($booking->payment && $booking->payment->records) {
                foreach ($booking->payment->records as $record) {
                    $record->update(['status' => 'FAILED']);
                }
            }

            Log::info("AutoCancelBooking: Booking ID {$booking->id} dibatalkan karena melewati batas waktu {$expire_time} menit.");
        }

        return $next($request);

    }
}
