<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegionalStatistic;

class RegionalStatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RegionalStatistic::updateOrCreate(
            ['tahun' => 2026],
            [
                'nama_kelurahan'       => 'Kelurahan Semampir',
                'nama_kecamatan'       => 'Kecamatan Kraksaan',
                'nama_kabupaten'       => 'Kabupaten Probolinggo',
                'luas_wilayah'         => 3.82,
                'jumlah_rt'            => 32,
                'jumlah_rw'            => 8,
                'total_penduduk'       => 5000,
                'pertumbuhan_penduduk' => 1.20,
                'jumlah_laki_laki'     => 4180,
                'jumlah_perempuan'     => 4245,
                'jumlah_kk'            => 2640,
                'usia_produktif'       => 5610,
                'usia_anak'            => 1825,
                'usia_lansia'          => 990,
                'sumber_data'          => 'SIAK Dispendukcapil',
                'is_active'            => true,
                'catatan'              => 'Data statistik wilayah resmi Kelurahan Semampir Tahun Anggaran 2026.',
            ]
        );
    }
}
