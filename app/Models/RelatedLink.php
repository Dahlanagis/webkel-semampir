<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'desc',
        'logo',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://') || str_starts_with($this->logo, 'data:image')) {
            return $this->logo;
        }

        return asset('storage/' . ltrim($this->logo, '/'));
    }
}
