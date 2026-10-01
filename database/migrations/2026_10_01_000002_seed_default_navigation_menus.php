<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\NavigationMenu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaults = [
            // PROFIL
            ['section' => 'profil', 'title' => 'Visi & Misi Kelurahan', 'url' => '/visi-misi', 'order' => 1, 'is_active' => true],
            ['section' => 'profil', 'title' => 'Struktur Organisasi (SOTK)', 'url' => '/struktur-organisasi', 'order' => 2, 'is_active' => true],
            ['section' => 'profil', 'title' => 'Sejarah & Asal Usul', 'url' => '/sejarah', 'order' => 3, 'is_active' => true],
            ['section' => 'profil', 'title' => 'Wilayah & Peta Geografis', 'url' => '/lokasi', 'order' => 4, 'is_active' => true],
            ['section' => 'profil', 'title' => 'Lembaga Kemasyarakatan', 'url' => '/halaman/lembaga-kemasyarakatan', 'order' => 5, 'is_active' => true],

            // LAYANAN
            ['section' => 'layanan', 'title' => 'Standar Pelayanan Publik', 'url' => '/standar-pelayanan', 'order' => 1, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Pelayanan KTP & Kartu Keluarga', 'url' => '/standar-pelayanan?id=2', 'order' => 2, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Surat Keterangan Tidak Mampu (SKTM)', 'url' => '/standar-pelayanan?id=3', 'order' => 3, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Surat Keterangan Usaha (SKU)', 'url' => '/standar-pelayanan?id=4', 'order' => 4, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Surat Keterangan Domisili Warga', 'url' => '/standar-pelayanan?id=5', 'order' => 5, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Pelayanan Pengantar Nikah (N1 - N4)', 'url' => '/standar-pelayanan?id=6', 'order' => 6, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Pelayanan Akta Kelahiran & Kematian', 'url' => '/standar-pelayanan?id=7', 'order' => 7, 'is_active' => true],
            ['section' => 'layanan', 'title' => 'Layanan Mandiri WhatsApp CS', 'url' => '/layanan-whatsapp', 'order' => 8, 'is_active' => true],

            // DOKUMEN
            ['section' => 'dokumen', 'title' => 'Pusat Unduhan & Arsip Digital', 'url' => '/dokumen', 'order' => 1, 'is_active' => true],
            ['section' => 'dokumen', 'title' => 'Laporan Akuntabilitas & Kinerja (LAKIP Semampir)', 'url' => '/dokumen?id=3', 'order' => 2, 'is_active' => true],
            ['section' => 'dokumen', 'title' => 'Rencana Strategis Pembangunan (Renstra 2024-2029)', 'url' => '/dokumen?id=4', 'order' => 3, 'is_active' => true],
            ['section' => 'dokumen', 'title' => 'Buku Panduan Standar Operasional Prosedur (SOP)', 'url' => '/dokumen?id=5', 'order' => 4, 'is_active' => true],
            ['section' => 'dokumen', 'title' => 'Blanko Formulir Permohonan & Surat Warga', 'url' => '/dokumen?id=6', 'order' => 5, 'is_active' => true],
            ['section' => 'dokumen', 'title' => 'Kompilasi Keputusan Lurah (SK) & Regulasi Wilayah', 'url' => '/dokumen?id=7', 'order' => 6, 'is_active' => true],
            ['section' => 'dokumen', 'title' => 'Monografi Wilayah & Data Statistik Kependudukan', 'url' => '/dokumen?id=8', 'order' => 7, 'is_active' => true],

            // INFORMASI
            ['section' => 'informasi', 'title' => 'Berita & Kabar Desa', 'url' => '/berita', 'order' => 1, 'is_active' => true],
            ['section' => 'informasi', 'title' => 'Pengumuman Warga', 'url' => '/pengumuman', 'order' => 2, 'is_active' => true],
            ['section' => 'informasi', 'title' => 'Agenda Kegiatan', 'url' => '/agenda', 'order' => 3, 'is_active' => true],
            ['section' => 'informasi', 'title' => 'APBDes & Transparansi', 'url' => '/transparansi', 'order' => 4, 'is_active' => true],
            ['section' => 'informasi', 'title' => 'Galeri Dokumentasi', 'url' => '/galeri', 'order' => 5, 'is_active' => true],
        ];

        foreach ($defaults as $item) {
            NavigationMenu::firstOrCreate(
                [
                    'section' => $item['section'],
                    'title'   => $item['title'],
                ],
                [
                    'url'       => $item['url'],
                    'order'     => $item['order'],
                    'is_active' => $item['is_active'],
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
