<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [
            // Galeri
            [
                'type' => 'galeri',
                'name' => 'Kegiatan',
                'slug' => 'kegiatan',
                'color_code' => 'emerald',
                'description' => 'Dokumentasi kegiatan resmi kelurahan dan kemasyarakatan.'
            ],
            [
                'type' => 'galeri',
                'name' => 'Pelayanan',
                'slug' => 'pelayanan',
                'color_code' => 'blue',
                'description' => 'Dokumentasi pelayanan administrasi dan jemput bola warga.'
            ],
            [
                'type' => 'galeri',
                'name' => 'Infrastruktur',
                'slug' => 'infrastruktur',
                'color_code' => 'amber',
                'description' => 'Dokumentasi pembangunan fisik dan perbaikan sarana prasarana.'
            ],
            [
                'type' => 'galeri',
                'name' => 'Budaya & Seni',
                'slug' => 'budaya-seni',
                'color_code' => 'purple',
                'description' => 'Dokumentasi kesenian daerah, festival, dan kebudayaan warga.'
            ],
            [
                'type' => 'galeri',
                'name' => 'Gotong Royong',
                'slug' => 'gotong-royong',
                'color_code' => 'teal',
                'description' => 'Dokumentasi kerja bakti lingkungan dan swadaya masyarakat.'
            ],
            [
                'type' => 'galeri',
                'name' => 'Kerja Bakti',
                'slug' => 'kerja-bakti',
                'color_code' => 'emerald',
                'description' => 'Aksi kerja bakti kebersihan dan sanitasi.'
            ],

            // Pengumuman
            [
                'type' => 'pengumuman',
                'name' => 'Informasi Umum',
                'slug' => 'informasi-umum',
                'color_code' => 'blue',
                'description' => 'Pengumuman informasi umum untuk warga kelurahan.'
            ],
            [
                'type' => 'pengumuman',
                'name' => 'Penting & Mendesak',
                'slug' => 'penting-mendesak',
                'color_code' => 'rose',
                'description' => 'Pengumuman penting yang memerlukan perhatian segera.'
            ],
            [
                'type' => 'pengumuman',
                'name' => 'Bantuan Sosial (Bansos)',
                'slug' => 'bansos',
                'color_code' => 'amber',
                'description' => 'Pemberitahuan pencairan dan verifikasi data bansos.'
            ],
            [
                'type' => 'pengumuman',
                'name' => 'Pelayanan Administrasi',
                'slug' => 'pelayanan-administrasi',
                'color_code' => 'emerald',
                'description' => 'Info seputar perubahan jadwal dan layanan administrasi.'
            ],

            // Dokumen
            [
                'type' => 'dokumen',
                'name' => 'Peraturan & Keputusan',
                'slug' => 'peraturan-keputusan',
                'color_code' => 'blue',
                'description' => 'SK Lurah dan dokumen regulasi resmi.'
            ],
            [
                'type' => 'dokumen',
                'name' => 'Standar Pelayanan (SOP)',
                'slug' => 'standar-pelayanan-sop',
                'color_code' => 'emerald',
                'description' => 'Standar operasional prosedur pengurusan surat dan layanan.'
            ],
            [
                'type' => 'dokumen',
                'name' => 'Transparansi Anggaran (APBD)',
                'slug' => 'transparansi-anggaran',
                'color_code' => 'amber',
                'description' => 'Laporan realisasi dan dokumen transparansi anggaran.'
            ],
            [
                'type' => 'dokumen',
                'name' => 'Formulir Persyaratan',
                'slug' => 'formulir-persyaratan',
                'color_code' => 'teal',
                'description' => 'Formulir blanko permohonan surat administrasi warga.'
            ],
        ];

        foreach ($categories as $item) {
            Category::firstOrCreate(
                ['type' => $item['type'], 'slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'color_code' => $item['color_code'],
                    'description' => $item['description']
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down action needed
    }
};
