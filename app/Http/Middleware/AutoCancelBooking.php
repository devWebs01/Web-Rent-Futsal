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
        // Cari booking yang belum dibayar dan sudah lebih dari 24 jam
        $canceledBookings = Booking::where('status', 'UNPAID')
            ->where('created_at', '<=', now()->subMinutes(1))
            ->update(['status' => 'CANCEL']);

        // Log jika ada booking yang dibatalkan
        if ($canceledBookings > 0) {
            Log::info("AutoCancelBooking: {$canceledBookings} booking dibatalkan karena melewati batas waktu 24 jam.");
        }

        return $next($request);
    }
}
