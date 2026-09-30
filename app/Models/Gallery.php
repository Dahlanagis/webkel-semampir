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
        'is_active',
    ];

    protected $casts = [
        'show_on_homepage' => 'boolean',
        'is_active' => 'boolean',
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
            // Hapus berkas fisik sampul
            if ($gallery->image) {
                \App\Http\Controllers\Admin\GalleryController::deletePhysicalFile($gallery->image);
            }
            
            // Hapus berkas fisik seluruh foto di album
            foreach ($gallery->images as $img) {
                if ($img->image_path) {
                    \App\Http\Controllers\Admin\GalleryController::deletePhysicalFile($img->image_path);
                }
                $img->delete();
            }
        });
    }
}
