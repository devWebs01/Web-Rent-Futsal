<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_name',
        'description',
        'status',
    ];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function bookingTimes()
    {
        return $this->hasMany(BookingTime::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
