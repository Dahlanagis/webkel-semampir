<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VillageProfileController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = storage_path('app/village_profile.json');
    }

    /**
     * Ambil data konfigurasi profil kelurahan.
     */
    public static function getProfileData(): array
    {
        $data = null;

        // 1. Baca dari database (tabel settings) agar persisten di Vercel / serverless
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settingVal = \App\Models\Setting::get('village_profile');
                if (!empty($settingVal)) {
                    $decoded = json_decode($settingVal, true);
                    if (is_array($decoded)) {
                        $data = $decoded;
                    }
                }
            }
        } catch (\Throwable $e) {}

        // 2. Fallback baca dari file JSON lokal
        if (!is_array($data)) {
            $path = storage_path('app/village_profile.json');
            if (!File::exists($path)) {
                $path = base_path('storage/app/village_profile.json');
            }

            if (File::exists($path)) {
                $data = json_decode(File::get($path), true);
            }
        }

        if (!is_array($data)) {
            $data = self::getDefaultProfileFallback();
        } else {
            if (!isset($data['stats'])) {
                $data['stats'] = self::getDefaultStats();
            }
            if (!isset($data['demographics'])) {
                $data['demographics'] = self::getDefaultDemographics();
            }
            if (!isset($data['apbd'])) {
                $data['apbd'] = self::getDefaultApbd();
            }
            if (!isset($data['territory'])) {
                $data['territory'] = self::getDefaultTerritory();
            }
            if (!isset($data['service_metrics'])) {
                $data['service_metrics'] = self::getDefaultServiceMetrics();
            }
        }

        // Sinkronisasi data kemitraan / link terkait dari tabel related_links jika ada
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('related_links')) {
                $dbLinks = \App\Models\RelatedLink::where('is_active', true)
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                if ($dbLinks->isNotEmpty()) {
                    $data['kemitraan'] = $dbLinks->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'url' => $item->url,
                            'desc' => $item->desc ?? '',
                            'logo' => $item->logo ?? '',
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback to json configuration
        }

        if (empty($data['kemitraan'])) {
            $data['kemitraan'] = self::getDefaultKemitraan();
        }

        // Sinkronisasi data alokasi APBD dari tabel apbdes jika tabel tersedia
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('apbdes')) {
                $year = (int) ($data['apbd']['year'] ?? date('Y'));
                $dbAllocations = \App\Models\Apbdes::where('tahun', $year)->orderBy('id', 'asc')->get();
                if ($dbAllocations->isEmpty()) {
                    $latestYear = \App\Models\Apbdes::max('tahun');
                    if ($latestYear) {
                        $dbAllocations = \App\Models\Apbdes::where('tahun', $latestYear)->orderBy('id', 'asc')->get();
                    }
                }
                if ($dbAllocations->isNotEmpty()) {
                    $data['apbd']['allocations'] = $dbAllocations->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'tahun' => $item->tahun,
                            'kode_rekening' => $item->kode_rekening,
                            'name' => $item->nama_bidang,
                            'amount' => number_format((float)$item->anggaran, 0, ',', '.'),
                            'pct' => rtrim(rtrim(number_format((float)$item->persentase, 2, ',', '.'), '0'), ','),
                            'desc' => $item->deskripsi ?? '',
                        ];
                    })->toArray();
                }
            }

            // Sinkronisasi data statistik wilayah dari model RegionalStatistic
            if (\Illuminate\Support\Facades\Schema::hasTable('regional_statistics')) {
                $regStat = \App\Models\RegionalStatistic::getActive();
                if ($regStat) {
                    $data['stats']['penduduk'] = number_format($regStat->total_penduduk, 0, ',', '.');
                    $data['stats']['kk'] = number_format($regStat->jumlah_kk, 0, ',', '.');
                    $data['stats']['rt_rw'] = sprintf('%02d / %02d', $regStat->jumlah_rt, $regStat->jumlah_rw);
                    $data['stats']['luas'] = rtrim(rtrim(number_format($regStat->luas_wilayah, 2, ',', '.'), '0'), ',') . ' km²';

                    $data['demographics']['total'] = number_format($regStat->total_penduduk, 0, ',', '.');
                    $data['demographics']['male'] = number_format($regStat->jumlah_laki_laki, 0, ',', '.');
                    $data['demographics']['female'] = number_format($regStat->jumlah_perempuan, 0, ',', '.');
                    $data['demographics']['productive_count'] = number_format($regStat->usia_produktif, 0, ',', '.');
                    $data['demographics']['productive_pct'] = (string) $regStat->persentase_usia_produktif;
                    $data['demographics']['child_count'] = number_format($regStat->usia_anak, 0, ',', '.');
                    $data['demographics']['child_pct'] = (string) $regStat->persentase_usia_anak;
                    $data['demographics']['elderly_count'] = number_format($regStat->usia_lansia, 0, ',', '.');
                    $data['demographics']['elderly_pct'] = (string) $regStat->persentase_usia_lansia;
                    $data['demographics']['avg_family_size'] = number_format($regStat->rata_rata_jiwa_per_kk, 2, ',', '.');
                    $data['demographics']['density'] = number_format($regStat->kepadatan_penduduk, 1, ',', '.');
                }
            }
        } catch (\Throwable $e) {
            // Fallback to json configuration
        }

        return $data;
    }

    /**
     * Default fallback profil jika file JSON belum tersedia.
     */
    public static function getDefaultProfileFallback(): array
    {
        return [
            'village_name' => 'Kelurahan Semampir',
            'subdistrict' => 'Kecamatan Kraksaan',
            'regency' => 'Kabupaten Probolinggo',
            'head_name' => 'Latif Hasan Asyari, SH.',
            'head_nip' => '19750612 201001 1 004',
            'sekel_nip' => '',
            'kasi_pem_nip' => '',
            'kasi_kesra_nip' => '',
            'kasi_ekbang_nip' => '',
            'head_photo' => 'profile/lurah_official_blue.jpg',
            'welcome_title' => 'Komitmen Pelayanan Publik yang Transparan, Cepat, & Responsif',
            'welcome_text' => '<p>Melalui sistem portal terpadu ini, Pemerintah Kelurahan Semampir berkomitmen penuh dalam mewujudkan pelayanan publik modern yang berbasis transparansi, kemudahan akses dokumen mandiri, dan akuntabilitas pengelolaan anggaran.</p><p>Kami terus berinovasi untuk memberikan pelayanan terbaik bagi warga Kraksaan tanpa kerumitan administrasi, ramah, akuntabel, dan 100% bebas dari segala bentuk pungutan liar.</p>',
            'vision' => 'Terwujudnya Pelayanan Publik Kelurahan Semampir yang Transparan, Akuntabel, Berbasis Digital, dan Berkelanjutan Demi Kesejahteraan Masyarakat.',
            'mission' => '<ol><li>Meningkatkan kualitas pelayanan administrasi kependudukan secara cepat dan tepat sasaran.</li><li>Mendorong transparansi pengelolaan informasi dan dana pembangunan kelurahan.</li><li>Mengembangkan pemberdayaan ekonomi warga berbasis kemitraan daerah.</li></ol>',
            'history_text' => '<p>Nama <strong>"Semampir"</strong> memiliki latar belakang sejarah etimologi yang berakar dari kata dasar <em>"Patok"</em>, yang berarti titik acuan penanda atau tiang pembatas wilayah.</p><p>Pada masa era kadipaten abad ke-18 dan masa pemerintahan kolonial di pesisir utara Probolinggo, wilayah ini difungsikan sebagai titik ukur nol dan acuan batas administrasi tanah wilayah Kraksaan. Di lokasi ini ditanam sebuah <strong>patok batu hitam besar</strong> yang menjadi semampir para musafir, pedagang, dan petugas karesidenan saat mengukur jarak jalur pos (De Grote Postweg).</p><p>Lambat laun, pemukiman di sekitar pilar patok penanda tersebut berkembang pesat dan akrab disapa warga dengan sebutan <strong>Dusun Semampir</strong>. Berkat letaknya yang sangat strategis di persimpangan jalan utama dan dekat dengan pusat perniagaan, wilayah ini terus bertumbuh menjadi desa pusat kegiatan masyarakat Kraksaan.</p>',
            'office_hours_mon_thu' => '08.00 - 15.30 WIB',
            'office_hours_fri' => '08.00 - 14.30 WIB',
            'phone' => '(0335) 841-209',
            'whatsapp' => '0812-3456-7890',
            'whatsapp_service_text' => 'Pemerintah Kelurahan Semampir menyediakan layanan WhatsApp untuk mempermudah Anda dalam mendapatkan informasi, menyampaikan pengaduan, atau menanyakan seputar pelayanan publik tanpa harus datang ke kantor kelurahan.',
            'email' => 'kelurahansemampir@probolinggokab.go.id',
            'address' => 'Jl. Pahlawan No. 01, Kelurahan Semampir, Kecamatan Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282',
            'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15822.464673673551!2d113.40748130833777!3d-7.756187513813955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd70068a4d4bf59%3A0x67a3f0196c810!2sSemampir%2C%20Kec.%20Kraksaan%2C%20Kabupaten%20Probolinggo%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1708412000000!5m2!1sid!2sid',
            'footer_description' => 'Website Resmi Kelurahan Semampir, Kecamatan Kraksaan, Kabupaten Probolinggo - Portal Informasi Publik, Pelayanan Administrasi Kependudukan, Surat Keterangan Online, Berita, dan Pembangunan Kemasyarakatan.',
            'social_facebook' => 'https://facebook.com/kelurahansemampir',
            'social_instagram' => 'https://instagram.com/kelurahansemampir',
            'social_youtube' => 'https://youtube.com/@kelurahansemampir',
            'social_tiktok' => 'https://tiktok.com/@kelurahansemampir',
            'social_whatsapp' => '0812-3456-7890',
            'qr_code_image' => null,
            'sekel_photo' => null,
            'lurah_tupoksi' => 'Penyelenggara utama urusan pemerintahan, ketertiban umum, pelayanan publik, dan pembinaan wilayah Semampir.',
            'sekel_tupoksi' => 'Pengelola administrasi umum, perencanaan operasional, keuangan, dan pelayanan surat-menyurat kelurahan.',
            'kasi_pem_tupoksi' => 'Pelayanan KTP/KK, pengawasan ketertiban lingkungan, dan pengelolaan data pertanahan & PBB.',
            'kasi_kesra_tupoksi' => 'Penerbitan SKTM, koordinasi bantuan sosial kementerian, kesehatan posyandu, dan keagamaan.',
            'kasi_ekbang_tupoksi' => 'Pemberdayaan masyarakat, pembinaan UMKM, fasilitasi pembangunan infrastruktur kelurahan, dan kebersihan lingkungan.',
            'stats' => self::getDefaultStats(),
            'demographics' => self::getDefaultDemographics(),
            'apbd' => self::getDefaultApbd(),
            'territory' => self::getDefaultTerritory(),
            'service_metrics' => self::getDefaultServiceMetrics(),
            'maklumat_text' => 'Dengan ini, kami seluruh ASN dan Pegawai Pemerintah Kelurahan Semampir menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji.',
            'maklumat_nomor_sk' => '188.45/04/426.411.01/2026',
            'maklumat_caption' => 'Dokumen Piagam Penetapan Standar Maklumat Pelayanan Publik Kelurahan Semampir Tahun 2026',
            'maklumat_file' => null,
            'maklumat_file_type' => null,
            'maklumat_file_name' => null,
            'maklumat_file_size' => null,
            'kemitraan' => self::getDefaultKemitraan(),
        ];
    }

    /**
     * Default list instansi kemitraan / link terkait.
     */
    public static function getDefaultKemitraan(): array
    {
        return [
            [
                'name' => 'Pemerintah Kabupaten Probolinggo',
                'url' => 'https://probolinggokab.go.id',
                'desc' => 'Portal Resmi Pemerintah Kabupaten Probolinggo',
                'logo' => 'kemitraan/8PqDzUpiMV2pJtnw6RRkzE7QRH0Gthhdlmhs6pnT.png',
            ],
            [
                'name' => 'Diskominfo Kab. Probolinggo',
                'url' => 'https://diskominfo.probolinggokab.go.id',
                'desc' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'logo' => 'kemitraan/AbNfqxBWSvM48OtGgXF1V2CO23Od9R2LrykyuiNz.png',
            ],
            [
                'name' => 'Dispendukcapil Kab. Probolinggo',
                'url' => 'https://dispendukcapil.probolinggokab.go.id',
                'desc' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'logo' => 'kemitraan/luNE2cYyAC8gM25HlmZAhkCnCcWPobkB5hS0401V.png',
            ],
            [
                'name' => 'Bapenda Kab. Probolinggo',
                'url' => 'https://bapenda.probolinggokab.go.id',
                'desc' => 'Badan Pendapatan Daerah (PBB-P2 & Pajak Daerah)',
                'logo' => 'kemitraan/RzhffVYrMgLzU8yDjtOmVnb7LYzVABR5azsqxHL0.png',
            ],
            [
                'name' => 'DLH Kab. Probolinggo',
                'url' => 'https://dlh.probolinggokab.go.id/',
                'desc' => 'Dinas Lingkungan Hidup Kabupaten Probolinggo',
                'logo' => 'kemitraan/TNtVrVuH16HN5lgd3Qe2jQfUhPM70BPOV2985dmN.png',
            ],
        ];
    }

    private static function getDefaultStats(): array
    {
        return [
            'penduduk' => '8.425',
            'kk' => '2.640',
            'rt_rw' => '32 / 08',
            'luas' => '3,82 km²',
        ];
    }

    private static function getDefaultDemographics(): array
    {
        return [
            'total' => '8.425',
            'male' => '4.180',
            'female' => '4.245',
            'productive_count' => '5.610',
            'productive_pct' => '66.6',
            'child_count' => '1.825',
            'child_pct' => '21.7',
            'elderly_count' => '990',
            'elderly_pct' => '11.7',
            'density' => '3.438',
            'avg_family_size' => '3.2',
            'occupations' => [
                ['name' => 'Pedagang / Pelaku UMKM Mikro', 'sector' => 'Perdagangan', 'count' => '1.840', 'pct' => '32.8'],
                ['name' => 'Karyawan Swasta & Jasa Komersial', 'sector' => 'Swasta', 'count' => '1.420', 'pct' => '25.3'],
                ['name' => 'Aparatur Sipil Negara (ASN / TNI / POLRI)', 'sector' => 'PNS/TNI/Polri', 'count' => '760', 'pct' => '13.5'],
                ['name' => 'Petani / Buruh Tani / Peternak', 'sector' => 'Pertanian', 'count' => '620', 'pct' => '11.0'],
                ['name' => 'Lainnya / Sektor Informal Mandiri', 'sector' => 'Lainnya', 'count' => '970', 'pct' => '17.4'],
            ],
            'educations' => [
                ['name' => 'Tamat SMA / SMK / Sederajat', 'count' => '3.240', 'pct' => '38.4'],
                ['name' => 'Diploma / Sarjana (D3, S1, S2, S3)', 'count' => '1.890', 'pct' => '22.4'],
                ['name' => 'Tamat SMP / Sederajat', 'count' => '1.620', 'pct' => '19.2'],
                ['name' => 'Tamat SD / Sederajat', 'count' => '1.215', 'pct' => '14.4'],
                ['name' => 'Belum / Tidak Sekolah', 'count' => '460', 'pct' => '5.6'],
            ],
        ];
    }

    private static function getDefaultApbd(): array
    {
        return [
            'year' => '2026',
            'total_budget' => '1.450.000.000',
            'realized_budget' => '985.400.000',
            'realized_pct' => '67.9',
            'allocations' => [
                ['id' => 1, 'tahun' => 2026, 'name' => 'Pembangunan Infrastruktur, Fisik, & Perbaikan Sanitasi Lingkungan', 'amount' => '652.500.000', 'pct' => '45', 'desc' => 'Pavingisasi jalan gang, normalisasi selokan RW 02-05, dan lampu penerangan jalan umum (PJU).'],
                ['id' => 2, 'tahun' => 2026, 'name' => 'Pemberdayaan Masyarakat, Kesejahteraan Sosial, & UMKM', 'amount' => '435.000.000', 'pct' => '30', 'desc' => 'Pelatihan digital marketing wirausaha muda, subsidi posyandu balita & lansia, serta bantuan bibit pekarangan.'],
                ['id' => 3, 'tahun' => 2026, 'name' => 'Operasional Penyelenggaraan Pelayanan & Administrasi Kantor', 'amount' => '362.500.000', 'pct' => '25', 'desc' => 'Infrastruktur server pelayanan mandiri digital, alat tulis kantor, pemeliharaan gedung, dan honor kebersihan.'],
            ]
        ];
    }

    private static function getDefaultTerritory(): array
    {
        return [
            'north' => 'Desa Kalibuntu',
            'east' => 'Kelurahan Kraksaan Wetan',
            'south' => 'Desa Alassumur Kulon',
            'west' => 'Desa Sidomukti',
            'schools' => '7',
            'mosques' => '12',
            'health' => '8',
            'markets' => '2',
            'rw' => '08',
            'rt' => '32',
        ];
    }

    private static function getDefaultServiceMetrics(): array
    {
        return [
            'avg_time' => '< 15 Menit',
            'ikm_score' => '98.4%',
        ];
    }

    public function identitasSambutan()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.identitas-sambutan', compact('profile'));
    }

    public function sotk()
    {
        $profile = self::getProfileData();
        return response()
            ->view('admin.beranda.sotk', compact('profile'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function visiMisiSejarah()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.visi-misi-sejarah', compact('profile'));
    }

    public function sejarah()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.sejarah', compact('profile'));
    }

    public function tupoksi()
    {
        $page = \App\Models\Page::firstOrCreate(
            ['slug' => 'tupoksi'],
            [
                'category' => 'profile',
                'title' => 'Tugas Pokok & Fungsi (Tupoksi)',
                'subtitle' => 'Landasan tugas pokok, wewenang, dan fungsi kerja aparatur Pemerintah Kelurahan.',
                'badge_text' => 'Tupoksi',
                'type' => 'standard',
                'is_active' => true,
                'order' => 6,
                'content' => '<h3>Tugas Pokok & Fungsi Kelurahan</h3><p>Kelurahan mempunyai tugas pokok menyelenggarakan urusan pemerintahan umum, ketentraman dan ketertiban umum, pemberdayaan masyarakat, serta pelayanan publik di tingkat kelurahan sesuai ketentuan peraturan perundang-undangan.</p>'
            ]
        );
        $profile = self::getProfileData();
        return view('admin.beranda.tupoksi', compact('page', 'profile'));
    }

    public function updateTupoksi(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'content' => 'required|string',
        ]);

        $page = \App\Models\Page::where('slug', 'tupoksi')->firstOrFail();
        $page->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('UPDATE', 'Memperbarui data Tugas Pokok & Fungsi (TUPOKSI)');

        return back()->with('status', 'Data Tugas Pokok & Fungsi (TUPOKSI) berhasil diperbarui.');
    }

    public function banner()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.banner', compact('profile'));
    }

    public function statistik()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.statistik', compact('profile'));
    }

    public function statistikWilayah()
    {
        $profile = self::getProfileData();
        $regionalStatistic = \App\Models\RegionalStatistic::getActive();
        return view('admin.beranda.statistik-wilayah', compact('profile', 'regionalStatistic'));
    }

    public function transparansi(Request $request)
    {
        $profile = self::getProfileData();

        $availableYears = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('apbdes')) {
                $availableYears = \App\Models\Apbdes::distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
            }
        } catch (\Throwable $e) {}

        $currentYear = (int) ($profile['apbd']['year'] ?? date('Y'));
        if (!in_array($currentYear, $availableYears)) {
            array_unshift($availableYears, $currentYear);
        }
        $availableYears = array_values(array_unique($availableYears));
        rsort($availableYears);

        $selectedYear = $request->query('tahun');
        if ($selectedYear === 'all') {
            $apbdesList = \App\Models\Apbdes::orderBy('tahun', 'desc')->orderBy('id', 'asc')->get();
        } elseif ($selectedYear && is_numeric($selectedYear)) {
            $selectedYear = (int)$selectedYear;
            $apbdesList = \App\Models\Apbdes::where('tahun', $selectedYear)->orderBy('id', 'asc')->get();
        } else {
            $selectedYear = $availableYears[0] ?? (int)date('Y');
            $apbdesList = \App\Models\Apbdes::where('tahun', $selectedYear)->orderBy('id', 'asc')->get();
        }

        return view('admin.beranda.transparansi', compact('profile', 'apbdesList', 'availableYears', 'selectedYear'));
    }

    public function kontak()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.kontak', compact('profile'));
    }

    public function lokasi()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.lokasi', compact('profile'));
    }

    public function footer()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.footer', compact('profile'));
    }

    /**
     * Perbarui data profil kelurahan (per bagian/section).
     */
    public function update(Request $request)
    {
        $request->validate([
            'head_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'sekel_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'kasi_pem_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'kasi_kesra_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'kasi_ekbang_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'history_hero_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'hero_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'qr_code_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'head_photo.image' => 'File ditolak! Foto pimpinan harus berupa file gambar.',
            'head_photo.mimes' => 'File ditolak! Format foto pimpinan harus JPG, PNG, atau WEBP.',
            'head_photo.max' => 'File ditolak! Ukuran foto pimpinan maksimal 5 MB.',
            'sekel_photo.mimes' => 'File ditolak! Format foto harus JPG, PNG, atau WEBP.',
            'sekel_photo.max' => 'File ditolak! Ukuran foto maksimal 5 MB.',
            'kasi_pem_photo.mimes' => 'File ditolak! Format foto harus JPG, PNG, atau WEBP.',
            'kasi_pem_photo.max' => 'File ditolak! Ukuran foto maksimal 5 MB.',
            'kasi_kesra_photo.mimes' => 'File ditolak! Format foto harus JPG, PNG, atau WEBP.',
            'kasi_kesra_photo.max' => 'File ditolak! Ukuran foto maksimal 5 MB.',
            'kasi_ekbang_photo.mimes' => 'File ditolak! Format foto harus JPG, PNG, atau WEBP.',
            'kasi_ekbang_photo.max' => 'File ditolak! Ukuran foto maksimal 5 MB.',
            'history_hero_image.mimes' => 'File ditolak! Format gambar sejarah harus JPG, PNG, atau WEBP.',
            'history_hero_image.max' => 'File ditolak! Ukuran gambar sejarah maksimal 5 MB.',
            'hero_image.mimes' => 'File ditolak! Format banner hero harus JPG, PNG, atau WEBP.',
            'hero_image.max' => 'File ditolak! Ukuran banner hero maksimal 5 MB.',
            'qr_code_image.mimes' => 'File ditolak! Format barcode QR harus JPG, PNG, atau WEBP.',
            'qr_code_image.max' => 'File ditolak! Ukuran barcode QR maksimal 5 MB.',
        ]);

        $existingData = self::getProfileData();
        $section = $request->input('section');
        $statusMsg = 'Profil kelurahan berhasil diperbarui.';

        if ($section === 'identitas_sambutan') {
            $existingData['village_name'] = $request->input('village_name', '');
            $existingData['head_name'] = $request->input('head_name', '');
            $existingData['head_nip'] = $request->input('head_nip', '');
            $existingData['welcome_title'] = $request->input('welcome_title', '');
            $welcomeText = $request->input('welcome_text', '');
            // Bersihkan styling inline warna pudar bekas copas dari website
            $welcomeText = preg_replace('/style="[^"]*(color:\s*(rgb\(203,\s*213,\s*225\)|#cbd5e1|rgb\(226,\s*232,\s*240\)|#e2e8f0)|background-color:\s*(rgb\(248,\s*250,\s*252\)|#f8fafc))[^"]*"/i', '', $welcomeText);
            $existingData['welcome_text'] = $welcomeText;

            if ($request->hasFile('head_photo')) {
                if (!empty($existingData['head_photo']) && Storage::disk('public')->exists($existingData['head_photo'])) {
                    Storage::disk('public')->delete($existingData['head_photo']);
                }
                $existingData['head_photo'] = $request->file('head_photo')->store('profile', 'public');
            }
            $statusMsg = 'Identitas Kelurahan & Sambutan berhasil diperbarui.';

        } elseif ($section === 'sotk') {
            if ($request->filled('head_name')) {
                $existingData['head_name'] = $request->input('head_name');
            }
            if ($request->has('head_nip')) {
                $existingData['head_nip'] = $request->input('head_nip', '');
            }
            // TUPOKSI Lurah: periksa apakah dikirim via lurah_tupoksi atau head_tupoksi
            if ($request->has('lurah_tupoksi') || $request->has('head_tupoksi')) {
                $rawLurahTup = $request->input('lurah_tupoksi', $request->input('head_tupoksi', ''));
                $existingData['lurah_tupoksi'] = html_entity_decode(trim((string)$rawLurahTup), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            $existingData['sekel_name'] = trim((string) $request->input('sekel_name', ''));
            $existingData['sekel_nip'] = trim((string) $request->input('sekel_nip', ''));
            $existingData['sekel_role'] = trim((string) $request->input('sekel_role', 'Sekretaris Kelurahan'));

            $existingData['kasi_pem_name'] = trim((string) $request->input('kasi_pem_name', ''));
            $existingData['kasi_pem_nip'] = trim((string) $request->input('kasi_pem_nip', ''));
            $existingData['kasi_pem_role'] = trim((string) $request->input('kasi_pem_role', 'Kasi Pemerintahan & Trantib'));

            $existingData['kasi_kesra_name'] = trim((string) $request->input('kasi_kesra_name', ''));
            $existingData['kasi_kesra_nip'] = trim((string) $request->input('kasi_kesra_nip', ''));
            $existingData['kasi_kesra_role'] = trim((string) $request->input('kasi_kesra_role', 'Kasi Pelayanan & Kesra'));

            $existingData['kasi_ekbang_name'] = trim((string) $request->input('kasi_ekbang_name', ''));
            $existingData['kasi_ekbang_nip'] = trim((string) $request->input('kasi_ekbang_nip', ''));
            $existingData['kasi_ekbang_role'] = trim((string) $request->input('kasi_ekbang_role', 'Kasi Pemberdayaan & Ekbang'));

            if ($request->has('sekel_tupoksi')) {
                $existingData['sekel_tupoksi'] = html_entity_decode(trim((string) $request->input('sekel_tupoksi', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            if ($request->has('kasi_pem_tupoksi')) {
                $existingData['kasi_pem_tupoksi'] = html_entity_decode(trim((string) $request->input('kasi_pem_tupoksi', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            if ($request->has('kasi_kesra_tupoksi')) {
                $existingData['kasi_kesra_tupoksi'] = html_entity_decode(trim((string) $request->input('kasi_kesra_tupoksi', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            if ($request->has('kasi_ekbang_tupoksi')) {
                $existingData['kasi_ekbang_tupoksi'] = html_entity_decode(trim((string) $request->input('kasi_ekbang_tupoksi', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            $sotkKeys = ['head_photo', 'sekel_photo', 'kasi_pem_photo', 'kasi_kesra_photo', 'kasi_ekbang_photo'];
            foreach ($sotkKeys as $photoKey) {
                $shortKey = str_replace('_photo', '', $photoKey);
                if ($request->input('delete_' . $shortKey . '_photo') == '1' || $request->input('delete_' . $photoKey) == '1') {
                    if (!empty($existingData[$photoKey]) && Storage::disk('public')->exists($existingData[$photoKey])) {
                        Storage::disk('public')->delete($existingData[$photoKey]);
                    }
                    $existingData[$photoKey] = '';
                }

                if ($request->hasFile($photoKey)) {
                    if (!empty($existingData[$photoKey]) && Storage::disk('public')->exists($existingData[$photoKey])) {
                        Storage::disk('public')->delete($existingData[$photoKey]);
                    }
                    $existingData[$photoKey] = $request->file($photoKey)->store('profile', 'public');
                }
            }

            // Proses Anggota / Pejabat Tambahan Dinamis
            $rawMembers = $request->input('members', []);
            $processedMembers = [];
            if (is_array($rawMembers)) {
                foreach ($rawMembers as $index => $m) {
                    $name = trim($m['name'] ?? '');
                    if ($name === '') {
                        continue;
                    }
                    $photoPath = trim($m['existing_photo'] ?? '');
                    if ($request->hasFile("member_photos.{$index}")) {
                        if (!empty($photoPath) && Storage::disk('public')->exists($photoPath)) {
                            Storage::disk('public')->delete($photoPath);
                        }
                        $photoPath = $request->file("member_photos.{$index}")->store('profile', 'public');
                    } elseif ($photoPath === '') {
                        $oldMemberPhoto = $existingData['sotk_members'][$index]['photo'] ?? null;
                        if (!empty($oldMemberPhoto) && Storage::disk('public')->exists($oldMemberPhoto)) {
                            Storage::disk('public')->delete($oldMemberPhoto);
                        }
                        $photoPath = null;
                    }
                    $processedMembers[] = [
                        'name' => $name,
                        'nip' => trim($m['nip'] ?? ''),
                        'position' => trim($m['position'] ?? 'Staf Kelurahan'),
                        'parent_key' => trim($m['parent_key'] ?? '') ?: null,
                        'photo' => $photoPath ?: '',
                        'tupoksi' => html_entity_decode(trim((string) ($m['tupoksi'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    ];
                }
            }
            $existingData['sotk_members'] = $processedMembers;

            $statusMsg = 'Struktur Organisasi (SOTK) dan data anggota berhasil diperbarui.';

        } elseif ($section === 'visi_misi' || $section === 'visi_misi_sejarah') {
            $existingData['vision'] = $request->input('vision', '');
            $existingData['mission'] = $request->input('mission', '');

            if ($request->has('history_text')) {
                $existingData['history_text'] = $request->input('history_text', '');
            }

            if ($request->hasFile('history_hero_image')) {
                if (!empty($existingData['history_hero_image']) && Storage::disk('public')->exists($existingData['history_hero_image'])) {
                    Storage::disk('public')->delete($existingData['history_hero_image']);
                }
                $existingData['history_hero_image'] = $request->file('history_hero_image')->store('profile', 'public');
            }
            $statusMsg = 'Visi dan Misi Kelurahan berhasil diperbarui.';

        } elseif ($section === 'sejarah') {
            $existingData['history_text'] = $request->input('history_text', '');

            if ($request->hasFile('history_hero_image')) {
                if (!empty($existingData['history_hero_image']) && Storage::disk('public')->exists($existingData['history_hero_image'])) {
                    Storage::disk('public')->delete($existingData['history_hero_image']);
                }
                $existingData['history_hero_image'] = $request->file('history_hero_image')->store('profile', 'public');
            }
            $statusMsg = 'Sejarah & Asal Usul Kelurahan berhasil diperbarui.';

        } elseif ($section === 'banner') {
            if ($request->hasFile('hero_image')) {
                if (!empty($existingData['hero_image']) && Storage::disk('public')->exists($existingData['hero_image'])) {
                    Storage::disk('public')->delete($existingData['hero_image']);
                }
                $existingData['hero_image'] = $request->file('hero_image')->store('profile', 'public');
            }
            $statusMsg = 'Hero Banner berhasil diperbarui.';

        } elseif ($section === 'kontak') {
            $existingData['office_hours_mon_thu'] = $request->input('office_hours_mon_thu', '');
            $existingData['office_hours_fri'] = $request->input('office_hours_fri', '');
            $existingData['phone'] = $request->input('phone', '');
            $existingData['whatsapp'] = $request->input('whatsapp', '');
            $existingData['whatsapp_service_text'] = $request->input('whatsapp_service_text', '');
            $existingData['email'] = $request->input('email', '');
            $statusMsg = 'Informasi Kontak dan Jam Operasional berhasil diperbarui.';

        } elseif ($section === 'lokasi') {
            $existingData['address'] = $request->input('address', '');
            $mapEmbed = $request->input('map_embed', '');
            if (preg_match('/src="([^"]+)"/', $mapEmbed, $matches)) {
                $mapEmbed = $matches[1];
            }
            $existingData['map_embed'] = $mapEmbed;
            $statusMsg = 'Alamat dan Peta Lokasi berhasil diperbarui.';

        } elseif ($section === 'footer') {
            $footerDesc = $request->input('footer_description', '');
            // Bersihkan styling inline warna pudar bekas copas dari website
            $footerDesc = preg_replace('/style="[^"]*(color:\s*(rgb\(203,\s*213,\s*225\)|#cbd5e1|rgb\(226,\s*232,\s*240\)|#e2e8f0)|background-color:\s*(rgb\(248,\s*250,\s*252\)|#f8fafc))[^"]*"/i', '', $footerDesc);
            $existingData['footer_description'] = $footerDesc;
            $existingData['social_facebook'] = $request->input('social_facebook', '');
            $existingData['social_instagram'] = $request->input('social_instagram', '');
            $existingData['social_youtube'] = $request->input('social_youtube', '');
            $existingData['social_tiktok'] = $request->input('social_tiktok', '');
            $existingData['social_whatsapp'] = $request->input('social_whatsapp', '');
            
            if ($request->hasFile('qr_code_image')) {
                if (!empty($existingData['qr_code_image']) && Storage::disk('public')->exists($existingData['qr_code_image'])) {
                    Storage::disk('public')->delete($existingData['qr_code_image']);
                }
                $existingData['qr_code_image'] = $request->file('qr_code_image')->store('profile', 'public');
            }
            $statusMsg = 'Info Footer & Media Sosial berhasil diperbarui.';

        } elseif ($section === 'statistik_dasar') {
            $existingData['stats'] = [
                'penduduk' => $request->input('stat_penduduk'),
                'kk' => $request->input('stat_kk'),
                'rt_rw' => $request->input('stat_rt_rw'),
                'luas' => $request->input('stat_luas'),
            ];
            $statusMsg = 'Statistik Dasar Beranda berhasil diperbarui.';

        } elseif ($section === 'demografi') {
            $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();

            $maleStr = $request->input('demo_male', '0');
            $maleNum = (float) str_replace(['.', ','], ['', '.'], $maleStr);

            $femaleStr = $request->input('demo_female', '0');
            $femaleNum = (float) str_replace(['.', ','], ['', '.'], $femaleStr);

            // Validasi: Nilai tidak boleh negatif (< 0)
            if ($maleNum < 0 || $femaleNum < 0) {
                return back()->withErrors(['Jumlah penduduk (Laki-laki atau Perempuan) tidak boleh bernilai negatif (< 0).'])->withInput();
            }

            // FORMULA OTOMATIS: total_penduduk = jumlah_pria + jumlah_wanita
            // Selalu dihitung ulang di backend sebelum disimpan ke database/storage untuk menjaga konsistensi data
            $totalNum = $maleNum + $femaleNum;
            $totalFormatted = number_format($totalNum, 0, ',', '.');
            $totalNumActual = $totalNum > 0 ? $totalNum : 1;

            // Kelompok Usia
            $prodStr = $request->input('demo_prod_count', '0');
            $prodNum = (float) str_replace(['.', ','], ['', '.'], $prodStr);

            $childStr = $request->input('demo_child_count', '0');
            $childNum = (float) str_replace(['.', ','], ['', '.'], $childStr);

            $eldStr = $request->input('demo_elderly_count', '0');
            $eldNum = (float) str_replace(['.', ','], ['', '.'], $eldStr);

            // Validasi non-negatif kelompok usia
            if ($prodNum < 0 || $childNum < 0 || $eldNum < 0) {
                return back()->withErrors(['Nilai kategori Kelompok Usia tidak boleh bernilai negatif (< 0).'])->withInput();
            }

            // Validasi batas maksimal: total penjumlahan kelompok usia <= total_penduduk
            $totalKelompokUsia = $prodNum + $childNum + $eldNum;
            if ($totalKelompokUsia > $totalNum) {
                return back()->withErrors([
                    "Jumlah Kelompok Usia tidak boleh melebihi total penduduk ({$totalFormatted} jiwa)."
                ])->withInput();
            }

            // Tingkat Pendidikan
            $eduNames = $request->input('edu_name', []);
            $eduCounts = $request->input('edu_count', []);
            $educations = [];
            $totalPendidikan = 0;

            foreach ($eduNames as $i => $name) {
                if (!empty($name)) {
                    $cStr = $eduCounts[$i] ?? '0';
                    $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);
                    if ($cNum < 0) {
                        return back()->withErrors(['Nilai kategori Tingkat Pendidikan tidak boleh bernilai negatif (< 0).'])->withInput();
                    }
                    $totalPendidikan += $cNum;
                    $educations[] = [
                        'name' => $name,
                        'count' => number_format($cNum, 0, ',', '.'),
                        'pct' => str_replace('.', ',', (string)round(($cNum / $totalNumActual) * 100, 1))
                    ];
                }
            }

            if ($totalPendidikan > $totalNum) {
                return back()->withErrors([
                    "Jumlah Tingkat Pendidikan tidak boleh melebihi total penduduk ({$totalFormatted} jiwa)."
                ])->withInput();
            }

            // Mata Pencaharian
            $occNames = $request->input('occ_name', []);
            $occSectors = $request->input('occ_sector', []);
            $occCounts = $request->input('occ_count', []);
            $occupations = [];
            $totalPekerjaan = 0;

            foreach ($occNames as $i => $name) {
                if (!empty($name)) {
                    $cStr = $occCounts[$i] ?? '0';
                    $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);
                    if ($cNum < 0) {
                        return back()->withErrors(['Nilai kategori Mata Pencaharian tidak boleh bernilai negatif (< 0).'])->withInput();
                    }
                    $totalPekerjaan += $cNum;
                    $occupations[] = [
                        'name' => $name,
                        'sector' => $occSectors[$i] ?? 'Lainnya',
                        'count' => number_format($cNum, 0, ',', '.'),
                        'pct' => str_replace('.', ',', (string)round(($cNum / $totalNumActual) * 100, 1))
                    ];
                }
            }

            if ($totalPekerjaan > $totalNum) {
                return back()->withErrors([
                    "Jumlah Mata Pencaharian tidak boleh melebihi total penduduk ({$totalFormatted} jiwa)."
                ])->withInput();
            }

            // Simpan data kependudukan
            $demographics['total'] = $totalFormatted;
            $demographics['male'] = number_format($maleNum, 0, ',', '.');
            $demographics['female'] = number_format($femaleNum, 0, ',', '.');

            $demographics['productive_count'] = number_format($prodNum, 0, ',', '.');
            $demographics['productive_pct'] = str_replace('.', ',', (string)round(($prodNum / $totalNumActual) * 100, 1));

            $demographics['child_count'] = number_format($childNum, 0, ',', '.');
            $demographics['child_pct'] = str_replace('.', ',', (string)round(($childNum / $totalNumActual) * 100, 1));

            $demographics['elderly_count'] = number_format($eldNum, 0, ',', '.');
            $demographics['elderly_pct'] = str_replace('.', ',', (string)round(($eldNum / $totalNumActual) * 100, 1));

            $demographics['density'] = $request->input('demo_density', '');
            $demographics['avg_family_size'] = $request->input('demo_avg_family', '');

            $demographics['occupations'] = $occupations;
            $demographics['educations'] = $educations;

            $existingData['demographics'] = $demographics;
            $existingData['stats']['penduduk'] = $totalFormatted;

            // Sinkronisasi otomatis ke tabel regional_statistics
            if (\Illuminate\Support\Facades\Schema::hasTable('regional_statistics')) {
                $regStat = \App\Models\RegionalStatistic::getActive();
                if ($regStat) {
                    $regStat->total_penduduk = (int)$totalNum;
                    $regStat->jumlah_laki_laki = (int)$maleNum;
                    $regStat->jumlah_perempuan = (int)$femaleNum;
                    $regStat->usia_produktif = (int)$prodNum;
                    $regStat->usia_anak = (int)$childNum;
                    $regStat->usia_lansia = (int)$eldNum;
                    $regStat->save();

                    \Illuminate\Support\Facades\Cache::forget('regional_statistic_' . $regStat->tahun);
                    \Illuminate\Support\Facades\Cache::forget('regional_statistic_active');
                }
            }

            $statusMsg = 'Data Demografi Lengkap berhasil diperbarui dan disinkronkan ke database statistik.';

        } elseif ($section === 'statistik_wilayah') {
            $validated = $request->validate([
                'tahun' => 'required|integer|min:2020|max:2050',
                'luas_wilayah' => 'required|numeric|min:0.01',
                'jumlah_rt' => 'required|integer|min:1',
                'jumlah_rw' => 'required|integer|min:1',
                'jumlah_kk' => 'required|integer|min:1',
                'pertumbuhan_penduduk' => 'nullable|numeric',
                'sumber_data' => 'required|string|max:255',
                'catatan' => 'nullable|string',
            ]);

            if (\Illuminate\Support\Facades\Schema::hasTable('regional_statistics')) {
                $regStat = \App\Models\RegionalStatistic::getActive($validated['tahun']);
                if (!$regStat) {
                    $regStat = new \App\Models\RegionalStatistic();
                    $regStat->tahun = $validated['tahun'];
                    $regStat->nama_kelurahan = 'Kelurahan Semampir';
                    $regStat->nama_kecamatan = 'Kecamatan Kraksaan';
                    $regStat->nama_kabupaten = 'Kabupaten Probolinggo';
                    $regStat->total_penduduk = 5000;
                    $regStat->jumlah_laki_laki = 4180;
                    $regStat->jumlah_perempuan = 4245;
                    $regStat->usia_produktif = 5610;
                    $regStat->usia_anak = 1825;
                    $regStat->usia_lansia = 990;
                    $regStat->is_active = true;
                }

                $regStat->luas_wilayah = (float) $validated['luas_wilayah'];
                $regStat->jumlah_rt = (int) $validated['jumlah_rt'];
                $regStat->jumlah_rw = (int) $validated['jumlah_rw'];
                $regStat->jumlah_kk = (int) $validated['jumlah_kk'];
                $regStat->pertumbuhan_penduduk = (float) ($validated['pertumbuhan_penduduk'] ?? 1.2);
                $regStat->sumber_data = $validated['sumber_data'];
                $regStat->catatan = $validated['catatan'] ?? null;
                $regStat->save();

                \Illuminate\Support\Facades\Cache::forget('regional_statistic_' . $regStat->tahun);
                \Illuminate\Support\Facades\Cache::forget('regional_statistic_active');

                // Sinkronkan ke struktur JSON profil
                $existingData['stats'] = [
                    'penduduk' => number_format($regStat->total_penduduk, 0, ',', '.'),
                    'kk' => number_format($regStat->jumlah_kk, 0, ',', '.'),
                    'rt_rw' => sprintf('%02d / %02d', $regStat->jumlah_rt, $regStat->jumlah_rw),
                    'luas' => rtrim(rtrim(number_format($regStat->luas_wilayah, 2, ',', '.'), '0'), ',') . ' km²',
                ];
            }

            $statusMsg = 'Data Statistik Wilayah (Luas, RT/RW, KK, dan Kepadatan) TA ' . $validated['tahun'] . ' berhasil diperbarui!';

        } elseif ($section === 'wilayah') {
            $existingData['territory'] = [
                'north' => $request->input('ter_north', ''),
                'east' => $request->input('ter_east', ''),
                'south' => $request->input('ter_south', ''),
                'west' => $request->input('ter_west', ''),
                'schools' => $request->input('ter_schools', ''),
                'mosques' => $request->input('ter_mosques', ''),
                'health' => $request->input('ter_health', ''),
                'markets' => $request->input('ter_markets', ''),
                'rw' => $request->input('ter_rw', ''),
                'rt' => $request->input('ter_rt', ''),
            ];
            $statusMsg = 'Profil Wilayah & Sarpras berhasil diperbarui.';

        } elseif ($section === 'layanan') {
            $existingData['service_metrics'] = [
                'avg_time' => $request->input('srv_time', ''),
                'ikm_score' => $request->input('srv_ikm', ''),
            ];
            $statusMsg = 'Metrik Capaian Layanan Publik berhasil diperbarui.';

        } elseif ($section === 'apbd') {
            $request->validate([
                'apbd_year' => 'required|integer|between:2000,2099',
                'apbd_total' => 'required',
                'apbd_realized' => 'required',
                'apbd_realized_pct' => 'required',
            ], [
                'apbd_year.required' => 'Tahun anggaran wajib diisi.',
                'apbd_year.integer' => 'Format tahun anggaran harus berupa angka 4 digit (YYYY).',
                'apbd_year.between' => 'Tahun anggaran harus berada dalam rentang tahun 2000 - 2099.',
            ]);

            $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
            $apbd['year'] = $request->input('apbd_year');
            $apbd['total_budget'] = $request->input('apbd_total', '');
            $apbd['realized_budget'] = $request->input('apbd_realized', '');
            $apbd['realized_pct'] = $request->input('apbd_realized_pct', '');

            $allocNames = $request->input('apbd_alloc_name', []);
            $allocAmounts = $request->input('apbd_alloc_amount', []);
            $allocPcts = $request->input('apbd_alloc_pct', []);
            $allocDescs = $request->input('apbd_alloc_desc', []);

            $allocations = [];
            foreach ($allocNames as $i => $name) {
                if (!empty($name)) {
                    $allocations[] = [
                        'name' => $name,
                        'amount' => $allocAmounts[$i] ?? '',
                        'pct' => $allocPcts[$i] ?? '',
                        'desc' => $allocDescs[$i] ?? ''
                    ];
                }
            }
            if (!empty($allocations)) {
                $apbd['allocations'] = $allocations;
            }
            $existingData['apbd'] = $apbd;
            $statusMsg = 'Transparansi APBD berhasil diperbarui.';
        }

        self::saveProfileData($existingData);

        ActivityLog::record('UPDATE', $statusMsg);

        return back()->with('status', $statusMsg)->with('success', $statusMsg);
    }

    /**
     * Simpan data konfigurasi profil kelurahan ke database (tabel settings) dan file lokal.
     */
    public static function saveProfileData(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // 1. Simpan ke tabel settings di database agar persisten di Vercel / serverless
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                \App\Models\Setting::set('village_profile', $json);
            }
        } catch (\Throwable $e) {}

        // 2. Simpan ke file lokal untuk lingkungan dev dan tracking repositori
        $savePaths = array_unique([
            storage_path('app/village_profile.json'),
            base_path('storage/app/village_profile.json'),
        ]);
        foreach ($savePaths as $savePath) {
            try {
                $dir = dirname($savePath);
                if (!File::isDirectory($dir)) {
                    @File::makeDirectory($dir, 0755, true);
                }
                @File::put($savePath, $json);
            } catch (\Throwable $e) {}
        }
    }

    public function storeApbd(Request $request)
    {
        $request->validate([
            'apbd_year' => 'required|integer|between:2000,2099',
            'apbd_alloc_name' => 'required|string|max:255',
            'apbd_alloc_amount' => 'required',
            'apbd_alloc_pct' => 'required',
            'apbd_alloc_desc' => 'nullable|string',
            'apbd_kode_rekening' => 'nullable|string|max:50',
        ], [
            'apbd_year.required' => 'Tahun anggaran wajib diisi.',
            'apbd_year.integer' => 'Format tahun anggaran harus berupa angka 4 digit (YYYY).',
            'apbd_year.between' => 'Tahun anggaran harus berada dalam rentang tahun 2000 - 2099.',
            'apbd_alloc_name.required' => 'Nama bidang alokasi wajib diisi.',
            'apbd_alloc_amount.required' => 'Jumlah anggaran wajib diisi.',
            'apbd_alloc_pct.required' => 'Persentase alokasi wajib diisi.',
        ]);

        $tahun = (int) $request->input('apbd_year');
        $namaBidang = trim($request->input('apbd_alloc_name'));

        // Cek constraint unik kombinasi (tahun, nama_bidang)
        $exists = \App\Models\Apbdes::where('tahun', $tahun)
            ->whereRaw('LOWER(TRIM(nama_bidang)) = ?', [strtolower($namaBidang)])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'apbd_alloc_name' => "Bidang anggaran '{$namaBidang}' untuk tahun anggaran {$tahun} sudah terdaftar. Silakan gunakan nama bidang lain atau perbarui data yang ada."
            ])->withInput();
        }

        $amountNum = (float) str_replace(['.', ','], ['', '.'], $request->input('apbd_alloc_amount', '0'));
        if ($amountNum < 0) {
            return back()->withErrors(['apbd_alloc_amount' => 'Nilai anggaran tidak boleh bernilai negatif (< 0).'])->withInput();
        }

        $pctNum = (float) str_replace(['%', ' '], '', str_replace(',', '.', $request->input('apbd_alloc_pct', '0')));
        if ($pctNum < 0 || $pctNum > 100) {
            return back()->withErrors(['apbd_alloc_pct' => 'Persentase alokasi anggaran harus berada di antara 0% dan 100%.'])->withInput();
        }

        \App\Models\Apbdes::create([
            'tahun' => $tahun,
            'kode_rekening' => $request->input('apbd_kode_rekening'),
            'nama_bidang' => $namaBidang,
            'anggaran' => $amountNum,
            'realisasi' => 0,
            'persentase' => $pctNum,
            'deskripsi' => $request->input('apbd_alloc_desc', ''),
        ]);

        $this->syncApbdJson($tahun);

        ActivityLog::record('CREATE', "Menambahkan pos alokasi APBDes '{$namaBidang}' (TA {$tahun})");

        return redirect()->route('admin.beranda.transparansi', ['tahun' => $tahun])
            ->with('success', "Bidang alokasi APBD '{$namaBidang}' (TA {$tahun}) berhasil ditambahkan.");
    }

    public function updateApbd(Request $request, $id)
    {
        $request->validate([
            'apbd_year' => 'required|integer|between:2000,2099',
            'apbd_alloc_name' => 'required|string|max:255',
            'apbd_alloc_amount' => 'required',
            'apbd_alloc_pct' => 'required',
            'apbd_alloc_desc' => 'nullable|string',
            'apbd_kode_rekening' => 'nullable|string|max:50',
        ], [
            'apbd_year.required' => 'Tahun anggaran wajib diisi.',
            'apbd_year.integer' => 'Format tahun anggaran harus berupa angka 4 digit (YYYY).',
            'apbd_year.between' => 'Tahun anggaran harus berada dalam rentang tahun 2000 - 2099.',
            'apbd_alloc_name.required' => 'Nama bidang alokasi wajib diisi.',
            'apbd_alloc_amount.required' => 'Jumlah anggaran wajib diisi.',
            'apbd_alloc_pct.required' => 'Persentase alokasi wajib diisi.',
        ]);

        $tahun = (int) $request->input('apbd_year');
        $namaBidang = trim($request->input('apbd_alloc_name'));

        // Cek constraint unik kombinasi (tahun, nama_bidang) kecualikan ID saat ini
        $exists = \App\Models\Apbdes::where('tahun', $tahun)
            ->whereRaw('LOWER(TRIM(nama_bidang)) = ?', [strtolower($namaBidang)])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'apbd_alloc_name' => "Bidang anggaran '{$namaBidang}' untuk tahun anggaran {$tahun} sudah terdaftar. Silakan gunakan nama bidang lain."
            ])->withInput();
        }

        $amountNum = (float) str_replace(['.', ','], ['', '.'], $request->input('apbd_alloc_amount', '0'));
        if ($amountNum < 0) {
            return back()->withErrors(['apbd_alloc_amount' => 'Nilai anggaran tidak boleh bernilai negatif (< 0).'])->withInput();
        }

        $pctNum = (float) str_replace(['%', ' '], '', str_replace(',', '.', $request->input('apbd_alloc_pct', '0')));
        if ($pctNum < 0 || $pctNum > 100) {
            return back()->withErrors(['apbd_alloc_pct' => 'Persentase alokasi anggaran harus berada di antara 0% dan 100%.'])->withInput();
        }

        $apbdes = \App\Models\Apbdes::find($id);
        if ($apbdes) {
            $apbdes->update([
                'tahun' => $tahun,
                'kode_rekening' => $request->input('apbd_kode_rekening', $apbdes->kode_rekening),
                'nama_bidang' => $namaBidang,
                'anggaran' => $amountNum,
                'persentase' => $pctNum,
                'deskripsi' => $request->input('apbd_alloc_desc', ''),
            ]);

            $this->syncApbdJson($tahun);

            ActivityLog::record('UPDATE', "Memperbarui pos alokasi APBDes '{$namaBidang}' (TA {$tahun})");

            return redirect()->route('admin.beranda.transparansi', ['tahun' => $tahun])
                ->with('success', "Data bidang alokasi '{$namaBidang}' berhasil diperbarui.");
        }

        // Fallback untuk array JSON jika id berupa index legacy
        $existingData = self::getProfileData();
        $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
        $allocations = $apbd['allocations'] ?? [];

        if (isset($allocations[$id])) {
            $allocations[$id] = [
                'tahun' => $tahun,
                'name' => $namaBidang,
                'amount' => $request->input('apbd_alloc_amount'),
                'pct' => $request->input('apbd_alloc_pct'),
                'desc' => $request->input('apbd_alloc_desc', '')
            ];

            $apbd['allocations'] = $allocations;
            $existingData['apbd'] = $apbd;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            ActivityLog::record('UPDATE', "Memperbarui pos alokasi APBDes '{$namaBidang}' (TA {$tahun})");

            return redirect()->route('admin.beranda.transparansi', ['tahun' => $tahun])
                ->with('success', 'Data bidang alokasi berhasil diperbarui.');
        }

        return back()->withErrors(['Bidang alokasi tidak ditemukan.']);
    }

    public function destroyApbd($id)
    {
        $apbdes = \App\Models\Apbdes::find($id);
        if ($apbdes) {
            $tahun = $apbdes->tahun;
            $nama = $apbdes->nama_bidang;
            $apbdes->delete();
            $this->syncApbdJson($tahun);

            ActivityLog::record('DELETE', "Menghapus pos alokasi APBDes '{$nama}' (TA {$tahun})");

            return redirect()->route('admin.beranda.transparansi', ['tahun' => $tahun])
                ->with('success', 'Bidang alokasi APBD berhasil dihapus.');
        }

        $existingData = self::getProfileData();
        $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
        $allocations = $apbd['allocations'] ?? [];

        if (isset($allocations[$id])) {
            $deletedName = $allocations[$id]['name'] ?? "ID #{$id}";
            array_splice($allocations, $id, 1);
            $apbd['allocations'] = $allocations;
            $existingData['apbd'] = $apbd;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            ActivityLog::record('DELETE', "Menghapus pos alokasi APBDes '{$deletedName}'");

            return back()->with('success', 'Bidang alokasi berhasil dihapus.');
        }

        return back()->withErrors(['Bidang alokasi tidak ditemukan.']);
    }

    public function storeStatistik(Request $request, $type)
    {
        $existingData = self::getProfileData();
        $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();
        $key = $type === 'education' ? 'educations' : 'occupations';
        $categoryName = $type === 'education' ? 'Tingkat Pendidikan' : 'Mata Pencaharian';
        $items = $demographics[$key] ?? [];

        $totalStr = $demographics['total'] ?? '0';
        $totalNum = (float) str_replace(['.', ','], ['', '.'], $totalStr);
        $totalFormatted = number_format($totalNum, 0, ',', '.');
        $totalNumActual = $totalNum > 0 ? $totalNum : 1;

        $cStr = $request->input('stat_count', '0');
        $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);

        // Validasi: Nilai tidak boleh negatif (< 0)
        if ($cNum < 0) {
            return back()->withErrors(["Nilai kategori {$categoryName} tidak boleh bernilai negatif (< 0)."])->withInput();
        }

        // Validasi batas maksimal: akumulasi kategori <= total_penduduk
        $currentSum = 0;
        foreach ($items as $item) {
            $currentSum += (float) str_replace(['.', ','], ['', '.'], $item['count'] ?? 0);
        }
        $newTotal = $currentSum + $cNum;

        if ($newTotal > $totalNum) {
            return back()->withErrors([
                "Jumlah {$categoryName} tidak boleh melebihi total penduduk ({$totalFormatted} jiwa)."
            ])->withInput();
        }

        $newItem = [
            'name' => $request->input('stat_name'),
            'count' => number_format($cNum, 0, ',', '.'),
            'pct' => str_replace('.', ',', (string)round(($cNum / $totalNumActual) * 100, 1))
        ];

        if ($type === 'occupation') {
            $newItem['sector'] = $request->input('stat_sector', 'Lainnya');
        }

        $items[] = $newItem;

        $demographics[$key] = $items;
        $existingData['demographics'] = $demographics;
        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        ActivityLog::record('CREATE', "Menambahkan data statistik {$categoryName}: {$request->input('stat_name')}");

        return back()->with('success', "Data {$categoryName} berhasil ditambahkan.");
    }

    public function updateStatistik(Request $request, $type, $index)
    {
        $existingData = self::getProfileData();
        $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();
        $key = $type === 'education' ? 'educations' : 'occupations';
        $categoryName = $type === 'education' ? 'Tingkat Pendidikan' : 'Mata Pencaharian';
        $items = $demographics[$key] ?? [];

        if (isset($items[$index])) {
            $totalStr = $demographics['total'] ?? '0';
            $totalNum = (float) str_replace(['.', ','], ['', '.'], $totalStr);
            $totalFormatted = number_format($totalNum, 0, ',', '.');
            $totalNumActual = $totalNum > 0 ? $totalNum : 1;

            $cStr = $request->input('stat_count', '0');
            $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);

            // Validasi: Nilai tidak boleh negatif (< 0)
            if ($cNum < 0) {
                return back()->withErrors(["Nilai kategori {$categoryName} tidak boleh bernilai negatif (< 0)."])->withInput();
            }

            // Validasi batas maksimal setelah update
            $currentSum = 0;
            foreach ($items as $idx => $item) {
                if ($idx != $index) {
                    $currentSum += (float) str_replace(['.', ','], ['', '.'], $item['count'] ?? 0);
                }
            }
            $newTotal = $currentSum + $cNum;

            if ($newTotal > $totalNum) {
                return back()->withErrors([
                    "Jumlah {$categoryName} tidak boleh melebihi total penduduk ({$totalFormatted} jiwa)."
                ])->withInput();
            }

            $updatedItem = [
                'name' => $request->input('stat_name'),
                'count' => number_format($cNum, 0, ',', '.'),
                'pct' => str_replace('.', ',', (string)round(($cNum / $totalNumActual) * 100, 1))
            ];

            if ($type === 'occupation') {
                $updatedItem['sector'] = $request->input('stat_sector', 'Lainnya');
            }

            $items[$index] = $updatedItem;

            $demographics[$key] = $items;
            $existingData['demographics'] = $demographics;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            ActivityLog::record('UPDATE', "Memperbarui data statistik {$categoryName}: {$request->input('stat_name')}");

            return back()->with('success', "Data {$categoryName} berhasil diperbarui.");
        }

        return back()->withErrors(['Data tidak ditemukan.']);
    }

    public function destroyStatistik($type, $index)
    {
        $existingData = self::getProfileData();
        $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();
        $key = $type === 'education' ? 'educations' : 'occupations';
        $categoryName = $type === 'education' ? 'Tingkat Pendidikan' : 'Mata Pencaharian';
        $items = $demographics[$key] ?? [];

        if (isset($items[$index])) {
            $deletedName = $items[$index]['name'] ?? "Index #{$index}";
            array_splice($items, $index, 1);
            $demographics[$key] = $items;
            $existingData['demographics'] = $demographics;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            ActivityLog::record('DELETE', "Menghapus data statistik {$categoryName}: {$deletedName}");

            return back()->with('success', 'Data statistik berhasil dihapus.');
        }

        return back()->withErrors(['Data tidak ditemukan.']);
    }

    /**
     * Helper untuk mensinkronisasi data APBD dari database ke file JSON profil.
     */
    protected function syncApbdJson($year = null): void
    {
        try {
            if (!File::exists($this->configPath)) {
                return;
            }
            $existingData = json_decode(File::get($this->configPath), true) ?: [];
            $targetYear = $year ?: ($existingData['apbd']['year'] ?? date('Y'));
            $dbAllocations = \App\Models\Apbdes::where('tahun', $targetYear)->orderBy('id', 'asc')->get();
            if ($dbAllocations->isNotEmpty()) {
                $existingData['apbd']['allocations'] = $dbAllocations->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'tahun' => $item->tahun,
                        'kode_rekening' => $item->kode_rekening,
                        'name' => $item->nama_bidang,
                        'amount' => number_format((float)$item->anggaran, 0, ',', '.'),
                        'pct' => rtrim(rtrim(number_format((float)$item->persentase, 2, ',', '.'), '0'), ','),
                        'desc' => $item->deskripsi ?? '',
                    ];
                })->toArray();
                File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        } catch (\Throwable $e) {}
    }
}
