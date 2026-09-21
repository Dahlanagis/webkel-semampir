<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gallery::truncate();

        $items = [
            // 1. Pemerintahan
            [
                'title' => 'Musrenbang Kelurahan Patokan Tahun 2026',
                'category' => 'Pemerintahan',
                'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pelaksanaan Musyawarah Perencanaan Pembangunan (Musrenbang) Kelurahan Patokan dalam merumuskan program prioritas pembangunan tahun 2026.',
            ],
            [
                'title' => 'Rapat Koordinasi Evaluasi Kinerja RT & RW',
                'category' => 'Pemerintahan',
                'image' => 'https://images.unsplash.com/photo-1577962917302-cd874c4e31d2?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Rapat koordinasi rutin Lurah Patokan bersama para Ketua RT dan RW se-Kelurahan Patokan guna peningkatan mutu pelayanan publik.',
            ],
            [
                'title' => 'Kunjungan Kerja Tim Penggerak PKK Kabupaten Probolinggo',
                'category' => 'Pemerintahan',
                'image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Kunjungan pembinaan adminitrasi dan program kerja PKK di Kantor Kelurahan Patokan Kecamatan Kraksaan.',
            ],

            // 2. Gotong Royong
            [
                'title' => 'Kerja Bakti Kebersihan Lingkungan & Saluran Air RW 02',
                'category' => 'Gotong Royong',
                'image' => 'https://images.unsplash.com/photo-1592417817098-8f3d6eb231fc?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Aksi gotong royong warga RW 02 membersihkan drainase dan pemotongan rumput menjelang musim penghujan.',
            ],
            [
                'title' => 'Penanaman 500 Bibit Pohon Penghijauan Lingkungan',
                'category' => 'Gotong Royong',
                'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Gerakan Patokan Hijau melalui penanaman bibit pohon buah dan pelindung di sepanjang jalan protokol kelurahan.',
            ],
            [
                'title' => 'Pembersihan Fasilitas Umum & Lapangan Warga RT 05',
                'category' => 'Gotong Royong',
                'image' => 'https://images.unsplash.com/photo-1588880331179-bc9b93a8cb5e?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Kerja bakti pembenahan sarana olahraga dan taman bermain anak bersama elemen karang taruna Patokan.',
            ],

            // 3. Posyandu & Kesehatan
            [
                'title' => 'Pelayanan Posyandu Balita & Imunisasi Srikandi',
                'category' => 'Posyandu & Kesehatan',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Kegiatan rutin penimbangan balita, pemberian makanan tambahan (PMT), dan imunisasi dasar di Posyandu Srikandi.',
            ],
            [
                'title' => 'Pemeriksaan Kesehatan Gratis & Cek Gula Darah Lansia',
                'category' => 'Posyandu & Kesehatan',
                'image' => 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pemeriksaan kesehatan gratis bagi warga lansia bekerjasama dengan Puskesmas Kraksaan.',
            ],
            [
                'title' => 'Sosialisasi PHBS & PSN Pemberantasan Sarang Nyamuk',
                'category' => 'Posyandu & Kesehatan',
                'image' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Edukasi Perilaku Hidup Bersih dan Sehat (PHBS) serta pemantauan jentik nyamuk mandiri di tiap rumah warga.',
            ],

            // 4. Pembangunan
            [
                'title' => 'Pembangunan & Pavingisasi Jalan Lingkungan RT 04',
                'category' => 'Pembangunan',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Realisasi anggaran pembangunan fisik berupa pemasangan paving blok di gang pemukiman warga RT 04.',
            ],
            [
                'title' => 'Perbaikan & Peremajaan Lampu Penerangan Jalan Umum',
                'category' => 'Pembangunan',
                'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pemasangan unit baru penerangan jalan umum (PJU) hemat energi untuk tingkatkan keamanan malam hari.',
            ],
            [
                'title' => 'Pembangunan Pos Keamanan Lingkungan (Poskamling) RW 01',
                'category' => 'Pembangunan',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Peresmian poskamling baru sarana ketertiban dan ketentraman warga di lingkungan RW 01 Kelurahan Patokan.',
            ],

            // 5. Kegiatan Sosial & Keagamaan
            [
                'title' => 'Penyaluran Bantuan Cadangan Pangan Beras Bagi Warga',
                'category' => 'Sosial & Keagamaan',
                'image' => 'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Distribusi bantuan pangan cadangan beras pemerintah kepada keluarga penerima manfaat di Pendopo Kelurahan Patokan.',
            ],
            [
                'title' => 'Pengajian Rutin & Doa Bersama Pengurus Kelurahan',
                'category' => 'Sosial & Keagamaan',
                'image' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Kegiatan keagamaan bimbingan rohani dan doa bersama jajaran staf kelurahan dan tokoh masyarakat.',
            ],
            [
                'title' => 'Santunan Anak Yatim & Dhuafa Kelurahan Patokan',
                'category' => 'Sosial & Keagamaan',
                'image' => 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb0?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pemberian santunan dan perlengkapan sekolah kepada anak-anak yatim dalam rangka kepedulian sosial kelurahan.',
            ],

            // 6. Pemberdayaan UMKM
            [
                'title' => 'Pelatihan Kewirausahaan & Branding Kemasan UMKM',
                'category' => 'Pemberdayaan UMKM',
                'image' => 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pelatihan pemasaran digital dan desain kemasan produk bagi para pelaku usaha kecil mikro di Kelurahan Patokan.',
            ],
            [
                'title' => 'Bazar Kuliner & Kerajinan Produk Unggulan Patokan',
                'category' => 'Pemberdayaan UMKM',
                'image' => 'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pameran dan bazar usaha mikro warga memperkenalkan produk jajanan tradisional dan kriya olahan rumahan.',
            ],
            [
                'title' => 'Sosialisasi Sertifikasi Halal & NIB Gratis Bagi Pelaku Usaha',
                'category' => 'Pemberdayaan UMKM',
                'image' => 'https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d?auto=format&fit=crop&w=1000&q=80',
                'caption' => 'Pendampingan pembuatan Nomor Induk Berusaha (NIB) dan perizinan sertifikasi halal gratis pelaku UMKM.',
            ],
        ];

        foreach ($items as $item) {
            Gallery::create($item);
        }
    }
}
