<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'image_path',
    ];

    /**
     * Get the field that owns the Facility
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
