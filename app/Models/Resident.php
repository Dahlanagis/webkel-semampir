<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $fillable = [
        'nik',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'marital_status',
        'occupation',
        'address',
        'rt',
        'rw',
        'phone_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope hanya penduduk aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
