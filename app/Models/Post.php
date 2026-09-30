<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'author',
        'excerpt',
        'content',
        'image',
        'views',
        'is_featured',
        'is_slider',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_slider' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get full image URL attribute for post image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=800&q=80';
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    protected static function booted()
    {
        static::deleting(function ($post) {
            if ($post->image && !str_starts_with($post->image, 'http') && \Illuminate\Support\Facades\Storage::disk('public')->exists($post->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image);
            }
        });
    }
}
