<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'category',
        'title',
        'subtitle',
        'badge_text',
        'slug',
        'type',
        'content',
        'banner_image',
        'order',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = \Illuminate\Support\Str::slug($page->title);
            }
            
            // Ensure unique slug
            $originalSlug = $page->slug;
            $count = 1;
            while (static::where('slug', $page->slug)->where('id', '!=', $page->id)->exists()) {
                $page->slug = "{$originalSlug}-{$count}";
                $count++;
            }
        });

        static::updating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = \Illuminate\Support\Str::slug($page->title);
            }
            
            $originalSlug = $page->slug;
            $count = 1;
            while (static::where('slug', $page->slug)->where('id', '!=', $page->id)->exists()) {
                $page->slug = "{$originalSlug}-{$count}";
                $count++;
            }
        });
    }
}
