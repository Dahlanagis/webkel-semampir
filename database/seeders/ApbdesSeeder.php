<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apbdes;

class ApbdesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ==========================================
            // TAHUN ANGGARAN 2026
            // ==========================================
            // 1. PENDAPATAN DESA 2026
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN ASLI DESA',
                'nama_bidang' => 'Bumdes (Hasil Usaha Desa)',
                'anggaran' => 80000000,
                'realisasi' => 80000000,
                'persentase' => 100.0,
                'deskripsi' => 'Hasil bagi laba operasional unit usaha BUMDes Semampir Makmur.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN ASLI DESA',
                'nama_bidang' => 'DANA DESA (DDS)',
                'anggaran' => 80000000,
                'realisasi' => 80000000,
                'persentase' => 100.0,
                'deskripsi' => 'Penerimaan PADes dari hasil pengelolaan aset desa.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN ASLI DESA',
                'nama_bidang' => 'DANA DESA (Tambahan Alokasi)',
                'anggaran' => 80000000,
                'realisasi' => 80000000,
                'persentase' => 100.0,
                'deskripsi' => 'Tambahan alokasi kinerja dan apresiasi pembinaan PADes.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN TRANSFER',
                'nama_bidang' => 'Dana Desa (DDS - APBN Pusat)',
                'anggaran' => 920000000,
                'realisasi' => 920000000,
                'persentase' => 100.0,
                'deskripsi' => 'Penyaluran Dana Desa tahap 1 dan tahap 2 dari APBN Pusat.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN TRANSFER',
                'nama_bidang' => 'Alokasi Dana Desa (ADD - APBD)',
                'anggaran' => 360000000,
                'realisasi' => 360000000,
                'persentase' => 100.0,
                'deskripsi' => 'Alokasi Dana Desa dari APBD Pemerintah Kabupaten Probolinggo.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN TRANSFER',
                'nama_bidang' => 'Bagi Hasil Pajak & Retribusi Daerah (PBH)',
                'anggaran' => 70000000,
                'realisasi' => 70000000,
                'persentase' => 100.0,
                'deskripsi' => 'Bagi hasil pajak daerah dan retribusi daerah tingkat kelurahan.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pendapatan',
                'kategori' => 'PENDAPATAN LAIN-LAIN',
                'nama_bidang' => 'Penerimaan Bantuan Keuangan & CSR Sah',
                'anggaran' => 20000000,
                'realisasi' => 20000000,
                'persentase' => 100.0,
                'deskripsi' => 'Penerimaan bantuan kemitraan program CSR lingkungan hidup.',
            ],

            // 2. BELANJA DESA 2026
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PENYELENGGARAAN PEMERINTAHAN DESA',
                'nama_bidang' => 'Operasional Kantor & Pelayanan Administrasi Warga',
                'anggaran' => 424600000,
                'realisasi' => 319600000,
                'persentase' => 75.3,
                'deskripsi' => 'Infrastruktur administrasi kantor, alat tulis, dan layanan digital.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PENYELENGGARAAN PEMERINTAHAN DESA',
                'nama_bidang' => 'kegiatan tr rw (Operasional RT / RW)',
                'anggaran' => 200000,
                'realisasi' => 200000,
                'persentase' => 100.0,
                'deskripsi' => 'Dukungan operasional lembaga RT dan RW.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PENYELENGGARAAN PEMERINTAHAN DESA',
                'nama_bidang' => 'kegiatan tr rw (Insentif & Rapat Koordinasi)',
                'anggaran' => 200000,
                'realisasi' => 200000,
                'persentase' => 100.0,
                'deskripsi' => 'Honorarium dan rapat koordinasi berkala RT/RW.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PELAKSANAAN PEMBANGUNAN DESA',
                'nama_bidang' => 'Pavingisasi Jalan Gang Lingkungan Warga (RW 03 & RW 04)',
                'anggaran' => 520000000,
                'realisasi' => 450000000,
                'persentase' => 86.5,
                'deskripsi' => 'Perkerasan jalan pemukiman dengan paving block K-250.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PELAKSANAAN PEMBANGUNAN DESA',
                'nama_bidang' => 'Normalisasi Saluran Drainase Utama Anti-Genangan',
                'anggaran' => 105000000,
                'realisasi' => 45000000,
                'persentase' => 42.9,
                'deskripsi' => 'Pembersihan sedimen dan pembangunan sudetan drainase.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PELAKSANAAN PEMBANGUNAN DESA',
                'nama_bidang' => 'Penerangan Jalan Umum (PJU) Surya Mandiri',
                'anggaran' => 45000000,
                'realisasi' => 45000000,
                'persentase' => 100.0,
                'deskripsi' => 'Pemasangan titik lampu jalan hemat energi tenaga surya.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PEMBINAAN & PEMBERDAYAAN MASYARAKAT',
                'nama_bidang' => 'Pembinaan Keamanan Lingkungan (Linmas) & PKK',
                'anggaran' => 190000000,
                'realisasi' => 138850000,
                'persentase' => 73.1,
                'deskripsi' => 'Pelatihan kesiapsiagaan linmas dan program kesehatan PKK.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'belanja',
                'kategori' => 'PEMBINAAN & PEMBERDAYAAN MASYARAKAT',
                'nama_bidang' => 'Pelatihan Kewirausahaan UMKM & Pencegahan Stunting',
                'anggaran' => 260000000,
                'realisasi' => 174000000,
                'persentase' => 66.9,
                'deskripsi' => 'Pemberian PMT gizi balita serta pelatihan pemasaran digital UMKM.',
            ],

            // 3. PEMBIAYAAN DESA 2026
            [
                'tahun' => 2026,
                'jenis' => 'pembiayaan',
                'kategori' => 'PENERIMAAN PEMBIAYAAN',
                'nama_bidang' => 'pembangunan jalan (Dana Cadangan Pembangunan)',
                'anggaran' => 1000000,
                'realisasi' => 1000000,
                'persentase' => 100.0,
                'deskripsi' => 'Pencairan dana cadangan infrastruktur.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pembiayaan',
                'kategori' => 'PENERIMAAN PEMBIAYAAN',
                'nama_bidang' => 'pembangunan jalan (Silpa Dana Fisik)',
                'anggaran' => 1000000,
                'realisasi' => 1000000,
                'persentase' => 100.0,
                'deskripsi' => 'Pemanfaatan sisa anggaran kegiatan fisik tahun sebelumnya.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pembiayaan',
                'kategori' => 'PENERIMAAN PEMBIAYAAN',
                'nama_bidang' => 'Sisa Lebih Perhitungan Anggaran (SiLPA) Tahun Lalu',
                'anggaran' => 43000000,
                'realisasi' => 43000000,
                'persentase' => 100.0,
                'deskripsi' => 'SiLPA tahun anggaran 2025 yang dialokasikan kembali.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pembiayaan',
                'kategori' => 'PENGELUARAN PEMBIAYAAN',
                'nama_bidang' => 'Penyertaan Modal BUMDes Mandiri',
                'anggaran' => 20000000,
                'realisasi' => 20000000,
                'persentase' => 100.0,
                'deskripsi' => 'Tambahan modal kerja untuk ekspansi toko pakan & sembako BUMDes.',
            ],
            [
                'tahun' => 2026,
                'jenis' => 'pembiayaan',
                'kategori' => 'PENGELUARAN PEMBIAYAAN',
                'nama_bidang' => 'Pembentukan Dana Cadangan Darurat',
                'anggaran' => 15000000,
                'realisasi' => 15000000,
                'persentase' => 100.0,
                'deskripsi' => 'Alokasi cadangan darurat penanganan tanggap bencana.',
            ],
        ];

        // Hapus dummy default lama id 1, 2, 3 jika ada
        Apbdes::whereIn('id', [1, 2, 3])->delete();

        foreach ($items as $item) {
            Apbdes::updateOrCreate(
                [
                    'tahun' => $item['tahun'],
                    'jenis' => $item['jenis'],
                    'nama_bidang' => $item['nama_bidang']
                ],
                $item
            );
        }
    }
}
