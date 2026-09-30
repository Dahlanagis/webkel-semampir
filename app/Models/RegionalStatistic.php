<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalStatistic extends Model
{
    use HasFactory;

    protected $table = 'regional_statistics';

    protected $fillable = [
        'tahun',
        'nama_kelurahan',
        'nama_kecamatan',
        'nama_kabupaten',
        'luas_wilayah',
        'jumlah_rt',
        'jumlah_rw',
        'total_penduduk',
        'pertumbuhan_penduduk',
        'jumlah_laki_laki',
        'jumlah_perempuan',
        'jumlah_kk',
        'usia_produktif',
        'usia_anak',
        'usia_lansia',
        'sumber_data',
        'is_active',
        'catatan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'luas_wilayah' => 'float',
        'jumlah_rt' => 'integer',
        'jumlah_rw' => 'integer',
        'total_penduduk' => 'integer',
        'pertumbuhan_penduduk' => 'float',
        'jumlah_laki_laki' => 'integer',
        'jumlah_perempuan' => 'integer',
        'jumlah_kk' => 'integer',
        'usia_produktif' => 'integer',
        'usia_anak' => 'integer',
        'usia_lansia' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Accessor: Menghitung Rata-rata Jiwa per Kepala Keluarga (KK).
     * Formula: Total Penduduk / Total KK
     *
     * @return float
     */
    public function getRataRataJiwaPerKkAttribute(): float
    {
        if ($this->jumlah_kk > 0) {
            return round($this->total_penduduk / $this->jumlah_kk, 2);
        }
        return 0.0;
    }

    /**
     * Accessor: Menghitung Kepadatan Penduduk (Jiwa/km²).
     * Formula: Total Penduduk / Luas Wilayah
     *
     * @return float
     */
    public function getKepadatanPendudukAttribute(): float
    {
        if ($this->luas_wilayah > 0) {
            return round($this->total_penduduk / $this->luas_wilayah, 2);
        }
        return 0.0;
    }

    /**
     * Accessor: Total Penduduk berdasarkan agregat Gender.
     *
     * @return int
     */
    public function getTotalGenderAttribute(): int
    {
        return $this->jumlah_laki_laki + $this->jumlah_perempuan;
    }

    /**
     * Accessor: Persentase Penduduk Laki-laki (%).
     *
     * @return float
     */
    public function getPersentaseLakiLakiAttribute(): float
    {
        $total = $this->total_gender > 0 ? $this->total_gender : $this->total_penduduk;
        return $total > 0 ? round(($this->jumlah_laki_laki / $total) * 100, 1) : 0.0;
    }

    /**
     * Accessor: Persentase Penduduk Perempuan (%).
     *
     * @return float
     */
    public function getPersentasePerempuanAttribute(): float
    {
        $total = $this->total_gender > 0 ? $this->total_gender : $this->total_penduduk;
        return $total > 0 ? round(($this->jumlah_perempuan / $total) * 100, 1) : 0.0;
    }

    /**
     * Accessor: Total Agregat Kelompok Usia (Jiwa).
     *
     * @return int
     */
    public function getTotalKelompokUsiaAttribute(): int
    {
        return $this->usia_produktif + $this->usia_anak + $this->usia_lansia;
    }

    /**
     * Accessor: Persentase Usia Kerja / Produktif (15-64 Tahun).
     *
     * @return float
     */
    public function getPersentaseUsiaProduktifAttribute(): float
    {
        $total = $this->total_kelompok_usia > 0 ? $this->total_kelompok_usia : $this->total_penduduk;
        return $total > 0 ? round(($this->usia_produktif / $total) * 100, 1) : 0.0;
    }

    /**
     * Accessor: Persentase Usia Anak (0-14 Tahun).
     *
     * @return float
     */
    public function getPersentaseUsiaAnakAttribute(): float
    {
        $total = $this->total_kelompok_usia > 0 ? $this->total_kelompok_usia : $this->total_penduduk;
        return $total > 0 ? round(($this->usia_anak / $total) * 100, 1) : 0.0;
    }

    /**
     * Accessor: Persentase Usia Lanjut / Lansia (65+ Tahun).
     *
     * @return float
     */
    public function getPersentaseUsiaLansiaAttribute(): float
    {
        $total = $this->total_kelompok_usia > 0 ? $this->total_kelompok_usia : $this->total_penduduk;
        return $total > 0 ? round(($this->usia_lansia / $total) * 100, 1) : 0.0;
    }

    /**
     * Scope query data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query data per tahun anggaran
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('tahun', $year);
    }

    /**
     * Ambil record data statistik aktif terbaru.
     *
     * @param int|null $year
     * @return self|null
     */
    public static function getActive(?int $year = null): ?self
    {
        $query = static::query();
        if ($year) {
            $query->where('tahun', $year);
        }
        return $query->where('is_active', true)->latest('tahun')->first()
            ?? static::latest('tahun')->first();
    }
}
