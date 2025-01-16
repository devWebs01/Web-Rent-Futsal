<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'alternative_phone',
    ];

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
