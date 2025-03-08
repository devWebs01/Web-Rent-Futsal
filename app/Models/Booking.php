<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice',
        'status',
        'total_price',
        'payment_method',
        'expired_at',
        'message',
    ];

    // Sinkronkan waktu dengan middleware AutoCancelBooking
    protected static function boot()
    {
        parent::boot();
        $expire_time = 10; // Waktu kadaluarsa dalam menit

        // Set expired_at otomatis saat booking dibuat
        static::creating(function ($booking) use ($expire_time) {
            if (! $booking->expired_at) {
                $booking->expired_at = now()->addMinutes($expire_time);
            }
        });
    }

    public function getTimeRemainingAttribute()
    {
        return $this->expired_at ? max(0, $this->expired_at->diffInSeconds(now())) : null;
    }

    // Relasi ke BookingTime
    public function bookingTimes()
    {
        return $this->hasMany(BookingTime::class);
    }

    // Relasi ke User (jika ada model User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment associated with the Booking
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
