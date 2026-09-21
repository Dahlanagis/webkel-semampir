<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RtRw extends Model
{
    protected $table = 'rt_rw';

    protected $fillable = [
        'type',
        'number',
        'head_name',
        'head_phone',
        'address',
        'total_kk',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_kk' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope hanya RT.
     */
    public function scopeRt($query)
    {
        return $query->where('type', 'RT');
    }

    /**
     * Scope hanya RW.
     */
    public function scopeRw($query)
    {
        return $query->where('type', 'RW');
    }

    /**
     * Mendapatkan label lengkap, misal: "RT 001" atau "RW 008"
     */
    public function getFullLabelAttribute(): string
    {
        return $this->type . ' ' . $this->number;
    }
}
