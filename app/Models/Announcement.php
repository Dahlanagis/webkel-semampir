<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'badge_type',
        'category_id',
        'link_url',
        'is_active',
        'is_urgent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
