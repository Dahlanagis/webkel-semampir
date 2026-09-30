<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apbdes;

class ApbdesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'tahun' => 2026,
                'kode_rekening' => '5.1.01',
                'nama_bidang' => 'Pembangunan Infrastruktur, Fisik, & Perbaikan Sanitasi Lingkungan',
                'anggaran' => 652500000,
                'realisasi' => 450000000,
                'persentase' => 45.0,
                'deskripsi' => 'Pavingisasi jalan gang, normalisasi selokan RW 02-05, dan lampu penerangan jalan umum (PJU).',
            ],
            [
                'tahun' => 2026,
                'kode_rekening' => '5.2.02',
                'nama_bidang' => 'Pemberdayaan Masyarakat, Kesejahteraan Sosial, & UMKM',
                'anggaran' => 435000000,
                'realisasi' => 310000000,
                'persentase' => 30.0,
                'deskripsi' => 'Pelatihan digital marketing wirausaha muda, subsidi posyandu balita & lansia, serta bantuan bibit pekarangan.',
            ],
            [
                'tahun' => 2026,
                'kode_rekening' => '5.3.01',
                'nama_bidang' => 'Operasional Penyelenggaraan Pelayanan & Administrasi Kantor',
                'anggaran' => 362500000,
                'realisasi' => 225400000,
                'persentase' => 25.0,
                'deskripsi' => 'Infrastruktur server pelayanan mandiri digital, alat tulis kantor, pemeliharaan gedung, dan honor kebersihan.',
            ],
            [
                'tahun' => 2025,
                'kode_rekening' => '5.1.01',
                'nama_bidang' => 'Pembangunan Saluran Irigasi & Drainase Lingkungan',
                'anggaran' => 520000000,
                'realisasi' => 520000000,
                'persentase' => 100.0,
                'deskripsi' => 'Penyelesaian jaringan drainase perkotaan kawasan RW 01 - RW 04.',
            ],
            [
                'tahun' => 2025,
                'kode_rekening' => '5.2.01',
                'nama_bidang' => 'Pengadaan Sarana Poskesdes & Timbangan Digital Posyandu',
                'anggaran' => 380000000,
                'realisasi' => 380000000,
                'persentase' => 100.0,
                'deskripsi' => 'Pengadaan alat ukur stunting dan peremajaan sarana posyandu balita kelurahan.',
            ],
        ];

        foreach ($items as $item) {
            Apbdes::updateOrCreate(
                ['tahun' => $item['tahun'], 'nama_bidang' => $item['nama_bidang']],
                $item
            );
        }
    }
}
