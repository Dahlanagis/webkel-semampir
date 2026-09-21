<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'type',
        'youtube_id',
        'category',
        'category_id',
        'caption',
        'show_on_homepage',
    ];

    protected $appends = ['image_url'];

    /**
     * Get full image URL attribute.
     */
    public function categoryModel()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'gallery_id');
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=800&q=80';
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    protected static function booted()
    {
        static::deleting(function ($gallery) {
            // Delete cover
            if ($gallery->image && !str_starts_with($gallery->image, 'http') && \Illuminate\Support\Facades\Storage::disk('public')->exists($gallery->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->image);
            }
            
            // Delete associated images
            foreach ($gallery->images as $img) {
                if ($img->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($img->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }
        });
    }
}
