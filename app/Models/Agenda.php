<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'description',
        'date',
        'time_start',
        'time_end',
        'location',
        'organizer',
        'coordinator',
        'status',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Scope agenda yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope agenda mendatang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
                     ->orderBy('date', 'asc')
                     ->orderBy('time_start', 'asc');
    }

    /**
     * Get label & style status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'ongoing' => [
                'label' => 'Sedang Berlangsung',
                'class' => 'bg-amber-100 text-amber-800 border-amber-200',
                'dot' => 'bg-amber-500 animate-pulse',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'bg-rose-100 text-rose-800 border-rose-200',
                'dot' => 'bg-rose-500',
            ],
            default => [
                'label' => 'Akan Datang',
                'class' => 'bg-blue-100 text-blue-800 border-blue-200',
                'dot' => 'bg-blue-500',
            ],
        };
    }
}
