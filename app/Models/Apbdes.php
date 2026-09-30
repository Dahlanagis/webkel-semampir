<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apbdes extends Model
{
    use HasFactory;

    protected $table = 'apbdes';

    protected $fillable = [
        'tahun',
        'kode_rekening',
        'nama_bidang',
        'anggaran',
        'realisasi',
        'persentase',
        'deskripsi',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'anggaran' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'persentase' => 'decimal:2',
    ];

    /**
     * Scope filter berdasarkan tahun anggaran.
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }
}
