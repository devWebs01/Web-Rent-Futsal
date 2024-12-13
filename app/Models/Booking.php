<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice',
        'status',
        'total_price',
        'payment_method',
    ];

    // Relasi ke BookingTime
    public function times()
    {
        return $this->hasMany(BookingTime::class);
    }

    // Relasi ke User (jika ada model User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
