<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'snapToken',
        'order_id',
        'gross_amount',
        'payment_time',
        'payment_type',
        'payment_detail',
        'status_message',
        'status',
    ];

    /**
     * Get the payment that owns the PaymentRecord
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(payment::class);
    }
}
