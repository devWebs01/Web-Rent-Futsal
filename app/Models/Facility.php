<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'facility_name',
    ];

    /**
     * Get the field that owns the Facility
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
