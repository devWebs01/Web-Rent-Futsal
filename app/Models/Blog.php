<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'tag',
        'thumbnail',
    ];

    /**
     * Boot method untuk menangani event model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            $blog->slug = static::generateUniqueSlug($blog->title);
        });

        static::updating(function ($blog) {
            // Hanya ubah slug jika title berubah
            if ($blog->isDirty('title')) {
                $blog->slug = static::generateUniqueSlug($blog->title, $blog->id);
            }
        });
    }

    /**
     * Generate unique slug untuk blog.
     */
    protected static function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $count = 0;

        while (static::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $count++;
            $slug = Str::slug($title).'-'.$count;
        }

        return $slug;
    }
}
