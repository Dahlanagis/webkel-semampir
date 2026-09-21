<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VillageDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $catBerita = Category::create([
            'name' => 'Berita Utama',
            'slug' => 'berita-utama',
            'description' => 'Seputar informasi kegiatan utama dan kabar terkini Kelurahan Patokan.',
            'color_code' => 'emerald',
        ]);

        $catPemerintahan = Category::create([
            'name' => 'Pemerintahan',
            'slug' => 'pemerintahan',
            'description' => 'Informasi kebijakan, peraturan, dan administrasi pemerintahan daerah.',
            'color_code' => 'blue',
        ]);

        $catPemberdayaan = Category::create([
            'name' => 'Pemberdayaan Masyarakat',
            'slug' => 'pemberdayaan-masyarakat',
            'description' => 'Program pelatihan, UMKM, dan kegiatan karang taruna warga.',
            'color_code' => 'amber',
        ]);

        $catKesehatan = Category::create([
            'name' => 'Kesehatan & Kebersihan',
            'slug' => 'kesehatan-kebersihan',
            'description' => 'Jadwal posyandu, kerja bakti, dan sanitasi lingkungan.',
            'color_code' => 'teal',
        ]);

        // 2. Announcements Ticker
        Announcement::create([
            'title' => 'Jadwal Pelayanan Mandiri Terpadu Kelurahan Patokan Buka Setiap Senin-Jumat Pukul 08.00 - 15.30 WIB',
            'content' => 'Warga diharapkan membawa dokumen fisik pendukung saat mengajukan permohonan surat keterangan.',
            'badge_type' => 'INFO',
            'is_active' => true,
            'is_urgent' => false,
        ]);

        Announcement::create([
            'title' => 'Kerja Bakti Masal Kebersihan Saluran Drainase Antisipasi Musim Hujan Hari Minggu Pagi',
            'content' => 'Seluruh Ketua RT dan RW dihimbau mengkoordinasikan warga di wilayah masing-masing.',
            'badge_type' => 'PENTING',
            'is_active' => true,
            'is_urgent' => true,
        ]);

        Announcement::create([
            'title' => 'Pencairan Bantuan Sosial Pangan Pokok Tahap III Tempat Pendopo Kelurahan Patokan',
            'content' => 'Harap membawa KTP Asli dan Kartu Keluarga.',
            'badge_type' => 'BANSOS',
            'is_active' => true,
            'is_urgent' => false,
        ]);

        // 3. Services Grid
        $servicesData = [
            [
                'title' => 'Surat Pengantar Umum',
                'slug' => 'surat-pengantar-umum',
                'icon' => 'document-text',
                'description' => 'Layanan pembuatan surat pengantar RT/RW dan Kelurahan untuk berbagai keperluan administrasi.',
                'requirement_info' => 'Membawa Pengantar RT/RW, Fotokopi KTP & KK yang berlaku.',
                'action_url' => '#',
                'badge_label' => 'Gratis / 15 Menit',
                'order' => 1,
            ],
            [
                'title' => 'KTP & Kartu Keluarga',
                'slug' => 'ktp-dan-kartu-keluarga',
                'icon' => 'identification',
                'description' => 'Pendampingan penerbitan KTP elektronik baru, penggantian rusak/hilang, dan pembaruan KK.',
                'requirement_info' => 'Pengantar RT/RW, Surat Hilang Polsek (jika hilang), KK Lama.',
                'action_url' => '#',
                'badge_label' => 'Disdukcapil Terpadu',
                'order' => 2,
            ],
            [
                'title' => 'Surat Keterangan Usaha (SKU)',
                'slug' => 'surat-keterangan-usaha',
                'icon' => 'building-storefront',
                'description' => 'Penerbitan surat keterangan bagi warga yang memiliki usaha mikro, kecil, dan menengah.',
                'requirement_info' => 'Pengantar RT/RW, KTP, Foto Lokasi Usaha & Jenis Usaha.',
                'action_url' => '#',
                'badge_label' => 'Prioritas UMKM',
                'order' => 3,
            ],
            [
                'title' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'slug' => 'surat-keterangan-tidak-mampu',
                'icon' => 'heart',
                'description' => 'Layanan permohonan SKTM untuk keperluan beasiswa sekolah, KIS, dan bantuan medis rumah sakit.',
                'requirement_info' => 'Pengantar RT/RW, KTP, KK, Pernyataan Tidak Mampu bermaterai.',
                'action_url' => '#',
                'badge_label' => 'Sosial & Bantuan',
                'order' => 4,
            ],
            [
                'title' => 'Pengaduan & Aspirasi Warga',
                'slug' => 'pengaduan-dan-aspirasi-warga',
                'icon' => 'chat-bubble-left-ellipsis',
                'description' => 'Kanal resmi penyampaian masukan, keluhan fasilitas umum, dan laporan lingkungan secara real-time.',
                'requirement_info' => 'Identitas pelapor terverifikasi (KTP Patokan).',
                'action_url' => '#',
                'badge_label' => 'Respon 24 Jam',
                'order' => 5,
            ],
            [
                'title' => 'Transparansi APBDes & Dana Kelurahan',
                'slug' => 'transparansi-apbdes',
                'icon' => 'banknotes',
                'description' => 'Laporan publikasi realisasi anggaran pendapatan dan belanja kelurahan tahun anggaran berjalan.',
                'requirement_info' => 'Dapat diunduh dokumen terbuka format PDF.',
                'action_url' => '#',
                'badge_label' => 'Keterbukaan Publik',
                'order' => 6,
            ],
        ];

        foreach ($servicesData as $serv) {
            Service::create($serv);
        }

        // 4. Posts (Berita & Feature Slider)
        $postsData = [
            [
                'title' => 'Kelurahan Patokan Luncurkan Portal Pelayanan Publik Digital Berbasis Mobile',
                'category_id' => $catPemerintahan->id,
                'author' => 'Lurah Patokan',
                'excerpt' => 'Guna meningkatkan mutu pelayanan publik yang cepat, akuntabel, dan ramah warga, Kelurahan Patokan menginisiasi sistem pelayanan mandiri digital.',
                'content' => 'Dalam rangka mendukung transformasi digital di Kabupaten Probolinggo, Kelurahan Patokan secara resmi merilis portal informasi terpadu. Portal ini memudahkan warga mengakses syarat pengurusan surat, mengecek transparansi anggaran, hingga menyampaikan pengaduan keluhan lingkungan secara langsung.',
                'image' => 'https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=1200&q=80',
                'views' => 428,
                'is_featured' => true,
                'is_slider' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Kerja Bakti Serentak Bersihkan Drainase Utama Antisipasi Genangan Air Musim Hujan',
                'category_id' => $catKesehatan->id,
                'author' => 'Sekretaris Kelurahan',
                'excerpt' => 'Ratusan warga dari RW 01 hingga RW 06 bahu membahu membersihkan sedimentasi lumpur dan sampah plastik pada saluran pembuangan utama.',
                'content' => 'Kegiatan gotong royong masal digelar pada hari Minggu pagi dengan melibatkan jajaran perangkat kelurahan, Babinsa, Bhabinkamtibmas, serta tokoh pemuda Karang Taruna. Aksi bersih-bersih ini difokuskan pada perbaikan aliran sungai kecil dan pembersihan selokan utama desa.',
                'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=1200&q=80',
                'views' => 312,
                'is_featured' => true,
                'is_slider' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Pelatihan Kewirausahaan & Pemasaran Digital bagi Pelaku UMKM Kelurahan Patokan',
                'category_id' => $catPemberdayaan->id,
                'author' => 'Kasi Pemberdayaan',
                'excerpt' => 'Sebanyak 40 pelaku UMKM lokal mengikuti bimbingan teknis pengemasan produk dan strategi berjualan via e-commerce dan sosial media.',
                'content' => 'Pemerintah Kelurahan Patokan terus mendorong kemandirian ekonomi masyarakat melalui pelatihan berkala. Peserta diajarkan teknik fotografi produk sederhana menggunakan smartphone serta pendaftaran sertifikasi halal gratis.',
                'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1200&q=80',
                'views' => 195,
                'is_featured' => false,
                'is_slider' => false,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Pemeriksaan Kesehatan Bebas Biaya & Posyandu Integrasi Lansia Balita',
                'category_id' => $catKesehatan->id,
                'author' => 'Tim Puskesmas Kraksaan',
                'excerpt' => 'Kader Posyandu bersama tenaga medis Puskesmas memberikan layanan cek gula darah, kolesterol, serta imunisasi rutin anak.',
                'content' => 'Kegiatan rutin bulanan ini mendapat antusiasme tinggi dari para lansia dan ibu hamil. Selain pemeriksaan fisik gratis, peserta juga menerima tambahan gizi berupa biskuit sehat dan susu kaya kalsium.',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80',
                'views' => 264,
                'is_featured' => false,
                'is_slider' => false,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Musyawarah Perencanaan Pembangunan (Musrenbangkel) Tahun Anggaran 2027',
                'category_id' => $catPemerintahan->id,
                'author' => 'Tim Penyusun RKPK',
                'excerpt' => 'Perwakilan tokoh masyarakat dan RT/RW menyepakati prioritas usulan pembangunan infrastruktur dan pemberdayaan sosial.',
                'content' => 'Musrenbangkel Patokan menetapkan tiga skala prioritas utama: pavingisasi jalan pemukiman RW 03, perbaikan penerangan jalan umum (PJU), dan penguatan kapasitas modal koperasi wanita kelurahan.',
                'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80',
                'views' => 520,
                'is_featured' => true,
                'is_slider' => false,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Gelar Seni Budaya & Bazar Kuliner Tradisional Sambut Hari Jadi Kabupaten Probolinggo',
                'category_id' => $catBerita->id,
                'author' => 'Panitia HUT',
                'excerpt' => 'Pementasan tarian lokal dan jajaran stan jajanan khas meramaikan lapangan utama alun-alun perkantoran kelurahan.',
                'content' => 'Acara pesta rakyat tahunan berlangsung meriah dan dihadiri oleh jajaran Forkopimcam Kraksaan. Acara ini ditutup dengan penyerahan penghargaan RT Terbersih dan Terinovatif.',
                'image' => 'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=1200&q=80',
                'views' => 640,
                'is_featured' => false,
                'is_slider' => false,
                'published_at' => now()->subDays(14),
            ]
        ];

        foreach ($postsData as $post) {
            $post['slug'] = Str::slug($post['title']);
            Post::create($post);
        }

        // 5. Galleries
        $galleriesData = [
            [
                'title' => 'Apel Pagi & Pembinaan Perangkat Kelurahan Patokan',
                'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=800&q=80',
                'category' => 'Pemerintahan',
                'caption' => 'Penegakan kedisiplinan aparat kelurahan untuk pelayanan prima.',
            ],
            [
                'title' => 'Peresmian Balai Posyandu RW 04',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80',
                'category' => 'Infrastruktur',
                'caption' => 'Pembangunan sarana prasarana kesehatan warga secara berswadaya.',
            ],
            [
                'title' => 'Kerja Bakti Pembersihan Taman & Lapangan Olahraga',
                'image' => 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?auto=format&fit=crop&w=800&q=80',
                'category' => 'Kegiatan',
                'caption' => 'Aksi nyata menciptakan lingkungan bersih dan asri.',
            ],
            [
                'title' => 'Penyaluran Bantuan Pangan Beras Kementan RI',
                'image' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80',
                'category' => 'Pelayanan',
                'caption' => 'Penyaluran bantuan beras tepat sasaran bagi warga penerima manfaat.',
            ],
            [
                'title' => 'Pementasan Seni Tari Tradisional Pemuda Karang Taruna',
                'image' => 'https://images.unsplash.com/photo-1469488865564-c2de10f69f96?auto=format&fit=crop&w=800&q=80',
                'category' => 'Budaya',
                'caption' => 'Pelestarian seni kearifan lokal oleh generasi muda.',
            ],
            [
                'title' => 'Monitoring & Evaluasi Pembangunan Pavingisasi RW 02',
                'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=800&q=80',
                'category' => 'Infrastruktur',
                'caption' => 'Pemeriksaan mutu pekerjaan fisik jalan lingkungan.',
            ],
        ];

        foreach ($galleriesData as $gal) {
            Gallery::create($gal);
        }
    }
}
