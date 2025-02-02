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
        $expire_time = 30; // Waktu kadaluarsa dalam menit

        $expiredBookings = Booking::where('status', 'UNPAID')
            ->where('created_at', '<=', now()->subMinutes($expire_time))
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'CANCEL']);

            foreach ($booking->payment->records as $record) {
                $record->update(['status' => 'FAILED']);
            }
        }

        // Log jika ada booking yang dibatalkan
        if ($expiredBookings->count() > 0) {
            foreach ($expiredBookings as $booking) {
                Log::info("AutoCancelBooking: {$booking} booking dibatalkan karena melewati batas waktu 24 jam.");
            }
        }

        return $next($request);
    }
}
