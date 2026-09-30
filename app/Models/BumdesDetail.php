<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BumdesDetail extends Model
{
    use HasFactory;

    protected $table = 'bumdes_detail';

    protected $fillable = [
        'lembaga_id',
        'nomor_badan_hukum_kemenkumham',
        'tahun_pendirian',
        'npwp_bumdes',
        'kategori_status',
        'permodalan_awal',
        'total_aset',
        'omzet_terakhir',
        'daftar_unit_usaha',
        'nama_penasihat',
        'nama_pelaksana_operasional',
        'nama_pengawas',
    ];

    protected $casts = [
        'tahun_pendirian' => 'integer',
        'permodalan_awal' => 'decimal:2',
        'total_aset' => 'decimal:2',
        'omzet_terakhir' => 'decimal:2',
    ];

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(LembagaDesa::class, 'lembaga_id');
    }

    /**
     * Badge visual status klasifikasi BUMDes.
     */
    public function getKategoriBadgeAttribute(): array
    {
        return match($this->kategori_status) {
            'Mandiri' => [
                'label' => 'Mandiri',
                'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            ],
            'Maju' => [
                'label' => 'Maju',
                'class' => 'bg-blue-100 text-blue-800 border-blue-300 font-bold',
            ],
            'Berkembang' => [
                'label' => 'Berkembang',
                'class' => 'bg-amber-100 text-amber-800 border-amber-300 font-bold',
            ],
            'Perintis' => [
                'label' => 'Perintis',
                'class' => 'bg-slate-100 text-slate-700 border-slate-300 font-medium',
            ],
            default => [
                'label' => $this->kategori_status ?? 'Berkembang',
                'class' => 'bg-slate-100 text-slate-700 border-slate-300',
            ],
        };
    }
}
