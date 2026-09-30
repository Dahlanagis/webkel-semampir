<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'color_code',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category', 'name');
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    /**
     * Cari ID kategori yang dipilih atau buat kategori baru jika diinput manual.
     */
    public static function resolveId(?string $categoryId, ?string $customCategory, string $type = 'berita'): ?int
    {
        $customCategory = trim((string)$customCategory);
        if (!empty($customCategory)) {
            $slug = \Illuminate\Support\Str::slug($customCategory);
            if (empty($slug)) {
                $slug = 'kat-' . time();
            }

            // Cari kategori yang sudah ada dengan type yang sama
            $existing = static::where('type', $type)
                ->where(function ($q) use ($customCategory, $slug) {
                    $q->where('name', $customCategory)
                      ->orWhere('slug', $slug);
                })->first();

            if ($existing) {
                return $existing->id;
            }

            // Buat slug unik jika ada benturan
            if (static::where('type', $type)->where('slug', $slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            $newCategory = static::create([
                'type' => $type,
                'name' => $customCategory,
                'slug' => $slug,
                'color_code' => 'emerald',
            ]);

            return $newCategory->id;
        }

        if (!empty($categoryId) && $categoryId !== 'manual' && is_numeric($categoryId)) {
            return (int)$categoryId;
        }

        return null;
    }
}
