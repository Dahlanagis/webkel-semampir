<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LembagaDesa extends Model
{
    use HasFactory;

    protected $table = 'lembaga_desa';

    protected $fillable = [
        'nama_lembaga',
        'singkatan',
        'slug',
        'jenis_lembaga',
        'nomor_sk_pendirian',
        'tanggal_sk',
        'dasar_hukum',
        'nama_ketua',
        'kontak',
        'alamat_kantor',
        'deskripsi_profil',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'status_aktif' => 'boolean',
    ];

    /**
     * Relasi 1-to-1 dengan data spesifik BUMDes jika jenis_lembaga == 'BUMDes'.
     */
    public function bumdesDetail(): HasOne
    {
        return $this->hasOne(BumdesDetail::class, 'lembaga_id');
    }

    /**
     * Scope lembaga aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Badge visual jenis lembaga.
     */
    public function getJenisBadgeAttribute(): array
    {
        return match($this->jenis_lembaga) {
            'LKD' => [
                'label' => 'Lembaga Kemasyarakatan (LKD)',
                'class' => 'bg-blue-50 text-blue-700 border-blue-200/60',
                'dot' => 'bg-blue-500'
            ],
            'BUMDes' => [
                'label' => 'Badan Usaha Desa (BUMDes)',
                'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200/60',
                'dot' => 'bg-emerald-500'
            ],
            'Lembaga Pemerintahan' => [
                'label' => 'Lembaga Pemerintahan',
                'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60',
                'dot' => 'bg-indigo-500'
            ],
            'Lembaga Adat' => [
                'label' => 'Lembaga Adat',
                'class' => 'bg-amber-50 text-amber-800 border-amber-200/60',
                'dot' => 'bg-amber-500'
            ],
            default => [
                'label' => $this->jenis_lembaga,
                'class' => 'bg-slate-50 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-500'
            ],
        };
    }
}
