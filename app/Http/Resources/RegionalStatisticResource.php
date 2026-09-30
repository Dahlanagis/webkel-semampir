<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionalStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'tahun_anggaran' => (int) $this->tahun,
            'wilayah' => [
                'kelurahan'   => $this->nama_kelurahan,
                'kecamatan'   => $this->nama_kecamatan,
                'kabupaten'   => $this->nama_kabupaten,
                'luas_km2'    => (float) $this->luas_wilayah,
                'luas_formatted' => number_format($this->luas_wilayah, 2, ',', '.') . ' km²',
            ],
            'administratif' => [
                'jumlah_rt'      => (int) $this->jumlah_rt,
                'jumlah_rw'      => (int) $this->jumlah_rw,
                'status'         => 'Aktif',
                'label'          => $this->jumlah_rt . ' RT & ' . $this->jumlah_rw . ' RW Aktif',
            ],
            'demografi' => [
                'total_penduduk'            => (int) $this->total_penduduk,
                'total_penduduk_formatted'  => number_format($this->total_penduduk, 0, ',', '.') . ' Jiwa',
                'pertumbuhan_tahunan'       => (float) $this->pertumbuhan_penduduk,
                'pertumbuhan_formatted'     => '+' . number_format($this->pertumbuhan_penduduk, 1, ',', '.') . '%',
                'total_kepala_keluarga'     => (int) $this->jumlah_kk,
                'total_kk_formatted'        => number_format($this->jumlah_kk, 0, ',', '.') . ' KK',
                'indikator_kalkulasi' => [
                    'rata_rata_jiwa_per_kk' => (float) $this->rata_rata_jiwa_per_kk,
                    'rata_rata_jiwa_label'  => number_format($this->rata_rata_jiwa_per_kk, 2, ',', '.') . ' Jiwa / KK',
                    'kepadatan_penduduk_km2'=> (float) $this->kepadatan_penduduk,
                    'kepadatan_label'       => number_format($this->kepadatan_penduduk, 2, ',', '.') . ' Jiwa / km²',
                ],
                'gender' => [
                    'laki_laki' => [
                        'jumlah'     => (int) $this->jumlah_laki_laki,
                        'formatted'  => number_format($this->jumlah_laki_laki, 0, ',', '.') . ' Jiwa',
                        'persentase' => (float) $this->persentase_laki_laki,
                    ],
                    'perempuan' => [
                        'jumlah'     => (int) $this->jumlah_perempuan,
                        'formatted'  => number_format($this->jumlah_perempuan, 0, ',', '.') . ' Jiwa',
                        'persentase' => (float) $this->persentase_perempuan,
                    ],
                    'rasio_label' => $this->persentase_laki_laki . '% : ' . $this->persentase_perempuan . '%',
                ],
                'kelompok_usia' => [
                    'usia_produktif' => [
                        'rentang'    => '15 - 64 Tahun',
                        'kategori'   => 'Usia Kerja / Produktif',
                        'jumlah'     => (int) $this->usia_produktif,
                        'formatted'  => number_format($this->usia_produktif, 0, ',', '.') . ' Jiwa',
                        'persentase' => (float) $this->persentase_usia_produktif,
                        'keterangan' => 'Bonus Demografi Tinggi',
                    ],
                    'anak_anak' => [
                        'rentang'    => '0 - 14 Tahun',
                        'kategori'   => 'Anak-anak & Pelajar',
                        'jumlah'     => (int) $this->usia_anak,
                        'formatted'  => number_format($this->usia_anak, 0, ',', '.') . ' Jiwa',
                        'persentase' => (float) $this->persentase_usia_anak,
                    ],
                    'lansia' => [
                        'rentang'    => '65+ Tahun',
                        'kategori'   => 'Usia Lanjut (Lansia)',
                        'jumlah'     => (int) $this->usia_lansia,
                        'formatted'  => number_format($this->usia_lansia, 0, ',', '.') . ' Jiwa',
                        'persentase' => (float) $this->persentase_usia_lansia,
                    ],
                ],
            ],
            'sumber_data' => $this->sumber_data,
            'is_active'   => (bool) $this->is_active,
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
