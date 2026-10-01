<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Agenda;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the village portal homepage.
     */
    public function index()
    {
        $announcements = Announcement::where('is_active', true)
            ->latest()
            ->get();

        $sliderPosts = Post::with('category')
            ->where('is_slider', true)
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(5)
            ->get();

        $services = \App\Models\ServiceType::where('is_active', true)
            ->where('show_on_homepage', true)
            ->orderBy('order', 'asc')
            ->get();

        $featuredPosts = Post::with('category')
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(2)->get();

        $neededPosts = 2 - $featuredPosts->count();
        $fallbackPosts = collect();
        
        if ($neededPosts > 0) {
            $fallbackPosts = Post::with('category')
                ->where('is_active', true)
                ->whereNotNull('published_at')
                ->where('is_featured', false)
                ->latest('published_at')
                ->take($neededPosts)->get();
        }

        $latestPosts = $featuredPosts->merge($fallbackPosts);

        $galleries = Gallery::where('is_active', true)->where('show_on_homepage', true)->latest()->get();
        if ($galleries->isEmpty()) {
            $galleries = Gallery::where('is_active', true)->latest()->take(6)->get();
        }

        $photoGalleries = Gallery::where('is_active', true)->where('show_on_homepage', true)
            ->where(function ($q) {
                $q->where('type', 'foto')->orWhereNull('type');
            })
            ->latest()
            ->get();
        if ($photoGalleries->isEmpty()) {
            $photoGalleries = Gallery::where('is_active', true)
                ->where(function ($q) {
                    $q->where('type', 'foto')->orWhereNull('type');
                })
                ->latest()
                ->take(6)
                ->get();
        }

        $videoGalleries = Gallery::where('show_on_homepage', true)
            ->where('type', 'video')
            ->latest()
            ->get();
        if ($videoGalleries->isEmpty()) {
            $videoGalleries = Gallery::where('is_active', true)
                ->where('type', 'video')
                ->latest()
                ->take(6)
                ->get();
        }

        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $stats = $villageProfile['stats'] ?? [
            'penduduk' => '8,425',
            'kk' => '2,640',
            'rt_rw' => '32 / 08',
            'luas' => '3.82 km²',
        ];

        $upcomingAgendas = Agenda::where('is_active', true)
            ->where('date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        $maklumatText = $villageProfile['maklumat_text'] ?? "Dengan ini, kami seluruh ASN dan Pegawai Pemerintah Kelurahan Semampir menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji.";
        $relatedLinks = $villageProfile['kemitraan'] ?? [];
        if (empty($relatedLinks)) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('related_links')) {
                    $relatedLinks = \App\Models\RelatedLink::where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->orderBy('id', 'asc')
                        ->get()
                        ->map(fn($item) => [
                            'id' => $item->id,
                            'name' => $item->name,
                            'url' => $item->url,
                            'desc' => $item->desc ?? '',
                            'logo' => $item->logo ?? '',
                        ])
                        ->toArray();
                }
            } catch (\Throwable $e) {}
        }
        if (empty($relatedLinks)) {
            $relatedLinks = \App\Http\Controllers\Admin\VillageProfileController::getDefaultKemitraan();
        }

        $regStat = \App\Models\RegionalStatistic::getActive();

        return view('home', compact(
            'announcements',
            'sliderPosts',
            'services',
            'latestPosts',
            'galleries',
            'photoGalleries',
            'videoGalleries',
            'stats',
            'villageProfile',
            'upcomingAgendas',
            'maklumatText',
            'relatedLinks',
            'regStat'
        ));
    }

    /**
     * Display the village organizational structure page.
     */
    public function visiMisi()
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        return view('visi-misi', compact('villageProfile'));
    }

    /**
     * Display the village organizational structure page.
     */
    public function strukturOrganisasi()
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        return view('struktur-organisasi', compact('villageProfile'));
    }

    /**
     * Display the brief history page of Kelurahan Semampir.
     */
    public function sejarah()
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        return view('sejarah', compact('villageProfile'));
    }

    /**
     * Display the location and address page.
     */
    public function lokasi()
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        return view('lokasi', compact('villageProfile'));
    }

    /**
     * Display the WhatsApp service info page.
     */
    public function layananWhatsapp()
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        return view('layanan-whatsapp', compact('villageProfile'));
    }

    /**
     * Display all published news/articles with pagination.
     */
    public function berita(Request $request)
    {
        $query = Post::with('category')->where('is_active', true)->whereNotNull('published_at')->latest('published_at');

        if ($request->filled('kategori')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->kategori));
        }

        $posts = $query->paginate(9)->withQueryString();
        $categories = \App\Models\Category::withCount(['posts' => fn($q) => $q->where('is_active', true)->whereNotNull('published_at')])->get();
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        return view('berita', compact('posts', 'categories', 'villageProfile'));
    }

    /**
     * Display a single news article by slug.
     */
    public function beritaDetail(string $slug)
    {
        $post = Post::with('category')->where('slug', $slug)->where('is_active', true)->whereNotNull('published_at')->firstOrFail();
        $post->increment('views');

        $relatedPosts = Post::with('category')
            ->whereNotNull('published_at')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        return view('berita-detail', compact('post', 'relatedPosts', 'villageProfile'));
    }

    /**
     * Display all gallery photos with pagination and category filtering.
     */
    public function galeri(Request $request)
    {
        $type = $request->query('type', 'foto');
        $query = Gallery::with(['images', 'categoryModel'])
            ->where('is_active', true)
            ->where(function ($q) use ($type) {
                if ($type === 'foto') {
                    $q->where('type', 'foto')->orWhereNull('type');
                } else {
                    $q->where('type', $type);
                }
            })
            ->latest();

        if ($request->filled('kategori')) {
            $query->where(function ($q) use ($request) {
                $q->where('category', $request->kategori)
                  ->orWhereHas('categoryModel', fn($c) => $c->where('name', $request->kategori));
            });
        }

        $galleries = $query->paginate(12)->withQueryString();

        $categories = \App\Models\Category::where('type', 'galeri')
            ->withCount(['galleries' => function($q) use ($type) {
                $q->where('is_active', true)->where(function ($sub) use ($type) {
                    if ($type === 'foto') {
                        $sub->where('type', 'foto')->orWhereNull('type');
                    } else {
                        $sub->where('type', $type);
                    }
                });
            }])
            ->having('galleries_count', '>', 0)
            ->get()
            ->map(fn($c) => (object)['category' => $c->name, 'total' => $c->galleries_count]);

        if ($categories->isEmpty()) {
            $categories = Gallery::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->where('is_active', true)
                ->whereNotNull('category')
                ->where(function ($q) use ($type) {
                    if ($type === 'foto') {
                        $q->where('type', 'foto')->orWhereNull('type');
                    } else {
                        $q->where('type', $type);
                    }
                })
                ->groupBy('category')
                ->get();
        }

        $totalPhotos = Gallery::where('is_active', true)->where(function ($q) use ($type) {
            if ($type === 'foto') {
                $q->where('type', 'foto')->orWhereNull('type');
            } else {
                $q->where('type', $type);
            }
        })->count();
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        return view('galeri', compact('galleries', 'categories', 'totalPhotos', 'villageProfile', 'type'));
    }

    /**
     * Display the budget transparency page.
     */
    /**
     * Display the budget transparency and demographic & social statistics page.
     */
    public function transparansi(\Illuminate\Http\Request $request)
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();

        // 1. Data Bantuan Sosial (Bansos)
        $socialAssistance = [
            'summary' => [
                'total_kpm' => 842,
                'persen_kk' => '31,9%',
                'total_anggaran_salur' => 'Rp 1,84 Miliar',
                'sisa_kuota' => 48,
                'total_kuota' => 890,
                'active_quota' => '842 / 890 KPM',
                'quota_pct' => 94.6,
                'update_period' => 'September 2026',
                'status_dtks' => 'Data Kesejahteraan Sosial Terpadu',
            ],
            'categories' => [
                [
                    'code' => 'BPNT',
                    'badge' => 'Bantuan Sembako',
                    'name' => 'Bantuan Pangan Non-Tunai (BPNT / Sembako)',
                    'tagline' => 'Bantuan Pangan Pokok Bergizi bagi Keluarga Prasejahtera',
                    'source' => 'Kementerian Sosial RI',
                    'nominal' => 'Rp 200.000 / bulan',
                    'recipients' => 340,
                    'quota' => 350,
                    'quota_pct' => 97.1,
                    'realization' => 'Rp 612.000.000',
                    'status' => 'Tersalurkan',
                    'status_badge' => 'emerald',
                    'disbursed_stage' => 'Alokasi September 2026',
                    'next_schedule' => 'Oktober 2026 di E-Warong / Agen Resmi',
                    'channel' => 'E-Warong Resmi / Agen Penyalur Terdaftar',
                    'description' => 'Bantuan pangan pokok yang ditransfer langsung ke rekening kartu warga untuk dibelanjakan bahan makanan di e-warong resmi.',
                    'main_criteria' => [
                        'Warga berpenghasilan rendah terdaftar dalam basis data kelurahan',
                        'Memiliki Kartu Keluarga Sejahtera (KKS) aktif',
                    ],
                    'criteria' => [
                        'Warga berpenghasilan rendah dalam data terpadu kesejahteraan sosial',
                        'Memiliki instrumen Kartu Keluarga Sejahtera (KKS) aktif',
                        'Bukan berstatus ASN, TNI, Polri, atau pensiunan',
                    ]
                ],
                [
                    'code' => 'PKH',
                    'badge' => 'Program Keluarga Harapan',
                    'name' => 'Program Keluarga Harapan (PKH)',
                    'tagline' => 'Bantuan Peningkatan Kesehatan Ibu-Anak & Pendidikan Sekolah',
                    'source' => 'Kementerian Sosial RI',
                    'nominal' => 'Rp 225.000 - Rp 750.000 / tahap',
                    'recipients' => 285,
                    'quota' => 300,
                    'quota_pct' => 95.0,
                    'realization' => 'Rp 712.500.000',
                    'status' => 'Sedang Berjalan',
                    'status_badge' => 'sky',
                    'disbursed_stage' => 'Tahap 3 Cair (Juli - September)',
                    'next_schedule' => 'November 2026 melalui Rekening Bank',
                    'channel' => 'Rekening Bank Himbara / KKS',
                    'description' => 'Bantuan sosial bersyarat bagi keluarga rentan untuk jaminan kesehatan balita/ibu hamil dan pendidikan anak sekolah.',
                    'main_criteria' => [
                        'Terdaftar aktif di sistem kesejahteraan sosial',
                        'Memiliki anak usia sekolah (SD/SMP/SMA) atau ibu hamil/balita',
                    ],
                    'criteria' => [
                        'Terdaftar aktif di data kesejahteraan sosial',
                        'Komponen Kesehatan: Ibu hamil atau anak usia balita',
                        'Komponen Pendidikan: Anak usia SD / SMP / SMA sederajat',
                        'Komponen Sosial: Lansia 60+ tahun atau disabilitas berat',
                    ]
                ],
                [
                    'code' => 'BLT',
                    'badge' => 'Bantuan Langsung Tunai',
                    'name' => 'Bantuan Langsung Tunai Kelurahan (BLT)',
                    'tagline' => 'Pengentasan Kemiskinan Ekstrem & Perlindungan Lansia',
                    'source' => 'Anggaran Bantuan Pemerintah',
                    'nominal' => 'Rp 300.000 / bulan',
                    'recipients' => 120,
                    'quota' => 120,
                    'quota_pct' => 100,
                    'realization' => 'Rp 324.000.000',
                    'status' => 'Tersalurkan',
                    'status_badge' => 'emerald',
                    'disbursed_stage' => 'Tahap IX (s.d. September 2026)',
                    'next_schedule' => 'Oktober 2026 di Kantor Kelurahan Semampir',
                    'channel' => 'Kantor Kelurahan Semampir / Bank Penyalur',
                    'description' => 'Bantuan tunai tanpa potongan yang disalurkan langsung kepada keluarga prasejahtera ekstrem dan lansia tunggal hasil Musyawarah Kelurahan.',
                    'main_criteria' => [
                        'Keluarga prasejahtera ber-KTP & KK Kelurahan Semampir',
                        'Tidak menerima bantuan dobel dari program bansos lain (PKH/BPNT)',
                    ],
                    'criteria' => [
                        'Keluarga prasejahtera ber-KTP dan KK Kelurahan Semampir',
                        'Kehilangan mata pencaharian pokok / penghasilan tidak menentu',
                        'Memiliki lansia tunggal atau anggota keluarga sakit menahun',
                        'Tidak menerima bansos dobel (bukan penerima PKH / BPNT)',
                    ]
                ],
                [
                    'code' => 'BST',
                    'badge' => 'Bansos Daerah',
                    'name' => 'Bantuan Sosial Tunai Daerah (BST)',
                    'tagline' => 'Jaring Pengaman Darurat Kemiskinan & Lansia Rentan',
                    'source' => 'Anggaran Pemerintah Daerah & Kelurahan',
                    'nominal' => 'Rp 300.000 / triwulan',
                    'recipients' => 97,
                    'quota' => 100,
                    'quota_pct' => 97.0,
                    'realization' => 'Rp 197.700.000',
                    'status' => 'Verifikasi Lapangan',
                    'status_badge' => 'amber',
                    'disbursed_stage' => 'Triwulan III',
                    'next_schedule' => 'Oktober 2026 di Kantor Kelurahan / Pos',
                    'channel' => 'Kantor Kelurahan Semampir / Pos Penyalur',
                    'description' => 'Bantuan sosial darurat yang disiapkan Pemda bagi keluarga rentan dan yatim yang belum terakomodasi bansos reguler.',
                    'main_criteria' => [
                        'Warga ber-KTP & domisili tetap Kelurahan Semampir',
                        'Memiliki Surat Keterangan Tidak Mampu (SKTM) aktif kelurahan',
                    ],
                    'criteria' => [
                        'Warga ber-KTP & domisili tetap Kelurahan Semampir',
                        'Mengantongi Surat Keterangan Tidak Mampu (SKTM) aktif kelurahan',
                        'Lolos verifikasi faktual lapangan petugas kelurahan & RT/RW',
                    ]
                ],
            ]
        ];

        // 2. Data Riwayat APBDes per Tahun (Collapsible / Accordion Multi-Year)
        $apbdesHistory = [
            [
                'tahun' => 2026,
                'status_label' => 'Tahun Berjalan (Realisasi Aktif)',
                'status_theme' => 'emerald',
                'is_current' => true,
                'total_pendapatan' => 1580000000,
                'total_belanja' => 1545000000,
                'realisasi_belanja' => 1127850000,
                'serapan_pct' => 73.0,
                'silpa' => 45000000,
                'surplus_defisit' => 35000000,
                'pembiayaan' => [
                    'penerimaan' => 45000000,
                    'pengeluaran' => 35000000,
                    'pembiayaan_netto' => 10000000,
                    'silpa_tahun_berjalan' => 45000000,
                    'silpa_desc' => 'Diproyeksikan sebagai cadangan kas kasir kelurahan dan kesiapan darurat kebencanaan akhir tahun.',
                ],
                'pendapatan_items' => [
                    ['kode' => '4.1', 'name' => 'Pendapatan Asli Desa (PADes / Tanah Kas & Retribusi)', 'amount' => 210000000, 'pct' => 13.3],
                    ['kode' => '4.2', 'name' => 'Dana Desa (DDS - APBN Pusat)', 'amount' => 920000000, 'pct' => 58.2],
                    ['kode' => '4.3', 'name' => 'Alokasi Dana Desa (ADD - APBD Kabupaten)', 'amount' => 360000000, 'pct' => 22.8],
                    ['kode' => '4.4', 'name' => 'Bagi Hasil Pajak & Retribusi Daerah (PBH)', 'amount' => 70000000, 'pct' => 4.4],
                    ['kode' => '4.5', 'name' => 'Pendapatan Lain-lain Sah / Bantuan CSR', 'amount' => 20000000, 'pct' => 1.3],
                ],
                'belanja_bidang' => [
                    [
                        'kode' => '5.1',
                        'bidang' => 'Bidang Penyelenggaraan Pemerintahan Desa',
                        'deskripsi' => 'Penghasilan tetap & tunjangan aparatur, operasional BPD, pengadaan ATK digital, langganan internet publik, dan perawatan berkala kantor pelayanan.',
                        'pagu' => 425000000,
                        'realisasi' => 320000000,
                        'serapan' => 75.3,
                        'color' => 'blue',
                        'programs' => [
                            'Operasional Layanan Administrasi Kependudukan Terpadu (SIAK)',
                            'Honorarium RT/RW & Pengurus Lembaga Kemasyarakatan',
                            'Pengadaan & Pemeliharaan Sarana Server Portal Kelurahan',
                        ]
                    ],
                    [
                        'kode' => '5.2',
                        'bidang' => 'Bidang Pelaksanaan Pembangunan Desa',
                        'deskripsi' => 'Pavingisasi jalan lingkungan RW 02-05, perbaikan saluran drainase anti-genangan, pengadaan lampu PJU hemat energi, dan revitalisasi posyandu terpadu.',
                        'pagu' => 670000000,
                        'realisasi' => 495000000,
                        'serapan' => 73.9,
                        'color' => 'emerald',
                        'programs' => [
                            'Pavingisasi Jalan Gang Pemukiman Warga RW 03 & RW 04 (480 m)',
                            'Normalisasi & Pemasangan U-Ditch Drainase Saluran Utama (260 m)',
                            'Pemasangan 35 Titik Penerangan Jalan Umum (PJU) Surya',
                        ]
                    ],
                    [
                        'kode' => '5.3',
                        'bidang' => 'Bidang Pembinaan Kemasyarakatan',
                        'deskripsi' => 'Pembinaan satuan perlindungan masyarakat (Linmas swakarsa), festival peringatan hari besar nasional & keagamaan, serta operasional Karang Taruna dan PKK.',
                        'pagu' => 190000000,
                        'realisasi' => 138850000,
                        'serapan' => 73.1,
                        'color' => 'amber',
                        'programs' => [
                            'Pelatihan Kesiapsiagaan Tim Tanggap Bencana & Linmas',
                            'Gelar Budaya & Gebyar HUT Kemerdekaan RI Kelurahan',
                            'Fasilitasi Kegiatan Pembinaan Kesejahteraan Keluarga (PKK)',
                        ]
                    ],
                    [
                        'kode' => '5.4',
                        'bidang' => 'Bidang Pemberdayaan Masyarakat',
                        'deskripsi' => 'Pelatihan pemasaran digital bagi pelaku UMKM kuliner & kerajinan, program ketahanan pangan pekarangan warga, bantuan bibit perikanan, dan insentif kader posyandu.',
                        'pagu' => 260000000,
                        'realisasi' => 174000000,
                        'serapan' => 66.9,
                        'color' => 'indigo',
                        'programs' => [
                            'Workshop Transformasi Digital & Sertifikasi Halal UMKM',
                            'Intervensi Pencegahan Stunting Balita & Suplemen Ibu Menyusui',
                            'Pengembangan Bibit Sayur & Kolam Ikan Bioflok RT Berdikari',
                        ]
                    ],
                ],
                'pdf_url' => asset('docs/transparansi-apbd.pdf'),
                'pdf_filename' => 'Laporan-Realisasi-APBDes-2026-Semampir.pdf',
                'infographic_url' => asset('images/logo.png'),
                'last_audit' => 'Inspektorat Daerah Kab. Probolinggo (Triwulan II - Status: WTP)',
            ],
            [
                'tahun' => 2025,
                'status_label' => 'Tahun Lalu (Laporan Pertanggungjawaban Sah)',
                'status_theme' => 'sky',
                'is_current' => false,
                'total_pendapatan' => 1485000000,
                'total_belanja' => 1460000000,
                'realisasi_belanja' => 1425800000,
                'serapan_pct' => 97.7,
                'silpa' => 45000000,
                'surplus_defisit' => 25000000,
                'pembiayaan' => [
                    'penerimaan' => 38000000,
                    'pengeluaran' => 25000000,
                    'pembiayaan_netto' => 13000000,
                    'silpa_tahun_berjalan' => 45000000,
                    'silpa_desc' => 'Telah diuji petik dan diaudit secara menyeluruh dengan predikat Wajar Tanpa Pengecualian (WTP).',
                ],
                'pendapatan_items' => [
                    ['kode' => '4.1', 'name' => 'Pendapatan Asli Desa (PADes)', 'amount' => 195000000, 'pct' => 13.1],
                    ['kode' => '4.2', 'name' => 'Dana Desa (DDS - APBN)', 'amount' => 880000000, 'pct' => 59.3],
                    ['kode' => '4.3', 'name' => 'Alokasi Dana Desa (ADD - APBD Kab)', 'amount' => 335000000, 'pct' => 22.6],
                    ['kode' => '4.4', 'name' => 'Bagi Hasil Pajak & Retribusi Daerah (PBH)', 'amount' => 60000000, 'pct' => 4.0],
                    ['kode' => '4.5', 'name' => 'Pendapatan Lain-lain Sah', 'amount' => 15000000, 'pct' => 1.0],
                ],
                'belanja_bidang' => [
                    [
                        'kode' => '5.1',
                        'bidang' => 'Bidang Penyelenggaraan Pemerintahan Desa',
                        'deskripsi' => 'Penggajian aparatur, operasional kantor kelurahan, pemeliharaan komputer inventaris, dan publikasi keterbukaan informasi.',
                        'pagu' => 410000000,
                        'realisasi' => 402500000,
                        'serapan' => 98.2,
                        'color' => 'blue',
                        'programs' => [
                            'Pelayanan Surat Mandiri Digital Warga',
                            'Peningkatan Kapasitas Aparatur Desa & Operator SIMDes',
                            'Operasional Koordinasi Kelembagaan RT/RW',
                        ]
                    ],
                    [
                        'kode' => '5.2',
                        'bidang' => 'Bidang Pelaksanaan Pembangunan Desa',
                        'deskripsi' => 'Pembangunan drainase beton sisi timur, perbaikan balai poskesdes, dan perbaikan gorong-gorong jalan perlintasan.',
                        'pagu' => 630000000,
                        'realisasi' => 618300000,
                        'serapan' => 98.1,
                        'color' => 'emerald',
                        'programs' => [
                            'Pembangunan Drainase Beton Jalan Mawar RT 08/RW 02',
                            'Rehabilitasi Gedung Posyandu Melati RW 05',
                            'Pemeliharaan Jalan Gang Warga Lingkungan Barat',
                        ]
                    ],
                    [
                        'kode' => '5.3',
                        'bidang' => 'Bidang Pembinaan Kemasyarakatan',
                        'deskripsi' => 'Pemberian perlengkapan operasional poskamling swakarsa, pentas seni kebudayaan daerah, dan festival olahraga Karang Taruna.',
                        'pagu' => 180000000,
                        'realisasi' => 174000000,
                        'serapan' => 96.7,
                        'color' => 'amber',
                        'programs' => [
                            'Pengadaan Rompi & Senter Satgas Keamanan Linmas',
                            'Turnamen Sepak Bola & Bola Voli Antar RW',
                            'Pelatihan Seni Hadrah & Tradisi Seni Warga',
                        ]
                    ],
                    [
                        'kode' => '5.4',
                        'bidang' => 'Bidang Pemberdayaan Masyarakat',
                        'deskripsi' => 'Program bantuan bibit lele bioflok, pelatihan olahan pangan lokal, serta edukasi gizi untuk menurunkan angka stunting desa.',
                        'pagu' => 240000000,
                        'realisasi' => 231000000,
                        'serapan' => 96.3,
                        'color' => 'indigo',
                        'programs' => [
                            'Pengadaan 10 Unit Kolam Terpal Bioflok Mandiri',
                            'Pelatihan Packaging & Izin PIRT Produk UMKM Keripik',
                            'Pemberian Makanan Tambahan (PMT) Pemulihan Gizi Balita',
                        ]
                    ],
                ],
                'pdf_url' => asset('docs/transparansi-apbd.pdf'),
                'pdf_filename' => 'Laporan-LPJ-Realisasi-APBDes-2025-Semampir.pdf',
                'infographic_url' => asset('images/logo.png'),
                'last_audit' => 'BPKP Perwakilan Jawa Timur (Opini: Wajar Tanpa Pengecualian)',
            ],
            [
                'tahun' => 2024,
                'status_label' => 'Arsip Historis (Laporan Telah Ditutup & Disahkan)',
                'status_theme' => 'slate',
                'is_current' => false,
                'total_pendapatan' => 1390000000,
                'total_belanja' => 1370000000,
                'realisasi_belanja' => 1332000000,
                'serapan_pct' => 97.2,
                'silpa' => 38000000,
                'surplus_defisit' => 20000000,
                'pembiayaan' => [
                    'penerimaan' => 32000000,
                    'pengeluaran' => 20000000,
                    'pembiayaan_netto' => 12000000,
                    'silpa_tahun_berjalan' => 38000000,
                    'silpa_desc' => 'Tercatat sah dalam berita acara kas daerah dan dialihkan ke pembukuan TA 2025.',
                ],
                'pendapatan_items' => [
                    ['kode' => '4.1', 'name' => 'Pendapatan Asli Desa (PADes)', 'amount' => 175000000, 'pct' => 12.6],
                    ['kode' => '4.2', 'name' => 'Dana Desa (DDS - APBN)', 'amount' => 830000000, 'pct' => 59.7],
                    ['kode' => '4.3', 'name' => 'Alokasi Dana Desa (ADD - APBD Kab)', 'amount' => 315000000, 'pct' => 22.7],
                    ['kode' => '4.4', 'name' => 'Bagi Hasil Pajak & Retribusi Daerah (PBH)', 'amount' => 55000000, 'pct' => 4.0],
                    ['kode' => '4.5', 'name' => 'Pendapatan Lain-lain Sah', 'amount' => 15000000, 'pct' => 1.0],
                ],
                'belanja_bidang' => [
                    [
                        'kode' => '5.1',
                        'bidang' => 'Bidang Penyelenggaraan Pemerintahan Desa',
                        'deskripsi' => 'Operasional rutin perkantoran, honorarium staf, dan penyusunan dokumen perencanaan desa (RPJMDes & RKPDes).',
                        'pagu' => 385000000,
                        'realisasi' => 378000000,
                        'serapan' => 98.2,
                        'color' => 'blue',
                        'programs' => [
                            'Penyusunan RKPDes & Musrenbang Kelurahan',
                            'Operasional Kebersihan & Listrik Gedung Pelayanan',
                            'Peremajaan Perangkat Komputer Front Office',
                        ]
                    ],
                    [
                        'kode' => '5.2',
                        'bidang' => 'Bidang Pelaksanaan Pembangunan Desa',
                        'deskripsi' => 'Pengaspalan jalan penghubung antar RW dan pembangunan jamban sehat keluarga prasejahtera.',
                        'pagu' => 590000000,
                        'realisasi' => 572000000,
                        'serapan' => 96.9,
                        'color' => 'emerald',
                        'programs' => [
                            'Pembangunan 20 Unit Jamban Sehat Warga Sanitasi Total',
                            'Pengaspalan Hotmix Jalan Lingkungan RW 01',
                            'Pengadaan Tempat Pembuangan Sampah Terpadu (TPST)',
                        ]
                    ],
                    [
                        'kode' => '5.3',
                        'bidang' => 'Bidang Pembinaan Kemasyarakatan',
                        'deskripsi' => 'Penguatan kerukunan warga, perlombaan kebersihan lingkungan, dan sarana olahraga remaja.',
                        'pagu' => 165000000,
                        'realisasi' => 160000000,
                        'serapan' => 97.0,
                        'color' => 'amber',
                        'programs' => [
                            'Festival Seni Santri & Shalawat Bersama Warga',
                            'Lomba Lingkungan Bersih & Sehat (Adipura Desa)',
                            'Bantuan Sarana Olahraga Tenis Meja & Bulutangkis',
                        ]
                    ],
                    [
                        'kode' => '5.4',
                        'bidang' => 'Bidang Pemberdayaan Masyarakat',
                        'deskripsi' => 'Pelatihan menjahit ibu rumah tangga dan pendampingan izin usaha nomor induk berusaha (NIB) bagi pedagang kaki lima.',
                        'pagu' => 230000000,
                        'realisasi' => 222000000,
                        'serapan' => 96.5,
                        'color' => 'indigo',
                        'programs' => [
                            'Pelatihan Menjahit & Bordir Mandiri Perempuan Kepala Keluarga',
                            'Fasilitasi 150 NIB (Nomor Induk Berusaha) Gratis',
                            'Gerakan Gemar Makan Ikan (Gemarikan) Balita',
                        ]
                    ],
                ],
                'pdf_url' => asset('docs/transparansi-apbd.pdf'),
                'pdf_filename' => 'Laporan-LPJ-Realisasi-APBDes-2024-Semampir.pdf',
                'infographic_url' => asset('images/logo.png'),
                'last_audit' => 'Inspektorat Daerah Kabupaten Probolinggo (Status: LHP Bersih)',
            ],
        ];

        return view('transparansi', compact('villageProfile', 'socialAssistance', 'apbdesHistory'));
    }

    /**
     * Display the public documents page.
     */
    public function dokumen(Request $request)
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        
        if ($request->filled('id')) {
            $document = \App\Models\Document::with('files')->where('is_active', true)->findOrFail($request->id);
            return view('dokumen-detail', compact('villageProfile', 'document'));
        }

        $query = \App\Models\Document::withCount('files')->where('is_active', true)->orderBy('created_at', 'desc')->orderBy('name', 'asc');
        $documents = $query->paginate(12)->withQueryString();

        return view('dokumen', compact('villageProfile', 'documents'));
    }

    /**
     * Display the standard public services page.
     */
    public function standarPelayanan()
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $services = \App\Models\ServiceType::where('is_active', true)->orderBy('order', 'asc')->get();

        return view('standar-pelayanan', compact('villageProfile', 'services'));
    }



    /**
     * Display the public announcements page.
     */
    public function pengumuman(Request $request)
    {
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        
        $query = \App\Models\Announcement::with('category')->where('is_active', true)->latest();
        
        if ($request->filled('kategori')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->kategori);
            });
        }
        
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(12)->withQueryString();
        $categories = \App\Models\Category::where('type', 'pengumuman')->orderBy('name', 'asc')->get();
        $activeCategory = $request->kategori;

        return view('pengumuman', compact('villageProfile', 'announcements', 'categories', 'activeCategory'));
    }

    public function page($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $villageProfile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        return view('page', compact('page', 'villageProfile'));
    }
}
