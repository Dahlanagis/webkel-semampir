<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    protected $fillable = [
        'tracking_code',
        'resident_name',
        'nik',
        'phone_number',
        'email',
        'subject',
        'category',
        'description',
        'attachment',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Auto-generate tracking code saat membuat aduan baru.
     */
    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (empty($complaint->tracking_code)) {
                $complaint->tracking_code = 'ADU-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Scope hanya aduan yang belum selesai.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['pending', 'in_review']);
    }
}
