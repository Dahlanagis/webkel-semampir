<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $path = storage_path('app/village_profile.json');
        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            if (is_array($data)) {
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
                return $data;
            }
        }

        // Default Fallback
        return [
            'village_name' => 'Kelurahan Patokan',
            'subdistrict' => 'Kecamatan Kraksaan',
            'regency' => 'Kabupaten Probolinggo',
            'head_name' => 'H. Ahmad Fauzi, S.STP, M.Si',
            'head_nip' => '19800512 200501 1 004',
            'head_photo' => null,
            'welcome_title' => 'Komitmen Pelayanan Publik yang Transparan, Cepat, & Responsif',
            'welcome_text' => '<p>Melalui sistem portal terpadu ini, Pemerintah Kelurahan Patokan berkomitmen penuh dalam mewujudkan pelayanan publik modern yang berbasis transparansi, kemudahan akses dokumen mandiri, dan akuntabilitas pengelolaan anggaran.</p><p>Kami terus berinovasi untuk memberikan pelayanan terbaik bagi warga Kraksaan tanpa kerumitan administrasi, ramah, akuntabel, dan 100% bebas dari segala bentuk pungutan liar.</p>',
            'vision' => 'Terwujudnya Pelayanan Publik Kelurahan Patokan yang Transparan, Akuntabel, Berbasis Digital, dan Berkelanjutan Demi Kesejahteraan Masyarakat.',
            'mission' => '<ol><li>Meningkatkan kualitas pelayanan administrasi kependudukan secara cepat dan tepat sasaran.</li><li>Mendorong transparansi pengelolaan informasi dan dana pembangunan kelurahan.</li><li>Mengembangkan pemberdayaan ekonomi warga berbasis kemitraan daerah.</li></ol>',
            'history_text' => '<p>Nama <strong>"Patokan"</strong> memiliki latar belakang sejarah etimologi yang berakar dari kata dasar <em>"Patok"</em>, yang berarti titik acuan penanda atau tiang pembatas wilayah.</p><p>Pada masa era kadipaten abad ke-18 dan masa pemerintahan kolonial di pesisir utara Probolinggo, wilayah ini difungsikan sebagai titik ukur nol dan acuan batas administrasi tanah wilayah Kraksaan. Di lokasi ini ditanam sebuah <strong>patok batu hitam besar</strong> yang menjadi patokan para musafir, pedagang, dan petugas karesidenan saat mengukur jarak jalur pos (De Grote Postweg).</p><p>Lambat laun, pemukiman di sekitar pilar patok penanda tersebut berkembang pesat dan akrab disapa warga dengan sebutan <strong>Dusun Patokan</strong>. Berkat letaknya yang sangat strategis di persimpangan jalan utama dan dekat dengan pusat perniagaan, wilayah ini terus bertumbuh menjadi desa pusat kegiatan masyarakat Kraksaan.</p>',
            'office_hours_mon_thu' => '08.00 - 15.30 WIB',
            'office_hours_fri' => '08.00 - 14.30 WIB',
            'phone' => '(0335) 841-209',
            'whatsapp' => '0812-3456-7890',
            'whatsapp_service_text' => 'Pemerintah Kelurahan Patokan menyediakan layanan WhatsApp untuk mempermudah Anda dalam mendapatkan informasi, menyampaikan pengaduan, atau menanyakan seputar pelayanan publik tanpa harus datang ke kantor kelurahan.',
            'email' => 'kelurahanpatokan@probolinggokab.go.id',
            'address' => 'Jl. Pahlawan No. 01, Kelurahan Patokan, Kecamatan Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282',
            'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15822.464673673551!2d113.40748130833777!3d-7.756187513813955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd70068a4d4bf59%3A0x67a3f0196c810!2sPatokan%2C%20Kec.%20Kraksaan%2C%20Kabupaten%20Probolinggo%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1708412000000!5m2!1sid!2sid',
            'footer_description' => 'Website Resmi Kelurahan Patokan, Kecamatan Kraksaan, Kabupaten Probolinggo - Portal Informasi Publik, Pelayanan Administrasi Kependudukan, Surat Keterangan Online, Berita, dan Pembangunan Kemasyarakatan.',
            'social_instagram' => '#',
            'social_youtube' => '#',
            'social_tiktok' => '#',
            'social_whatsapp' => '#',
            'qr_code_image' => null,
            'sekel_photo' => null,
            'lurah_tupoksi' => 'Penyelenggara utama urusan pemerintahan, ketertiban umum, pelayanan publik, dan pembinaan wilayah Patokan.',
            'sekel_tupoksi' => 'Pengelola administrasi umum, perencanaan operasional, keuangan, dan pelayanan surat-menyurat kelurahan.',
            'kasi_pem_tupoksi' => 'Pelayanan KTP/KK, pengawasan ketertiban lingkungan, dan pengelolaan data pertanahan & PBB.',
            'kasi_kesra_tupoksi' => 'Penerbitan SKTM, koordinasi bantuan sosial kementerian, kesehatan posyandu, dan keagamaan.',
            'kasi_ekbang_tupoksi' => 'Pemberdayaan masyarakat, pembinaan UMKM, fasilitasi pembangunan infrastruktur kelurahan, dan kebersihan lingkungan.',
            'stats' => self::getDefaultStats(),
            'demographics' => self::getDefaultDemographics(),
            'apbd' => self::getDefaultApbd(),
            'territory' => self::getDefaultTerritory(),
            'service_metrics' => self::getDefaultServiceMetrics(),
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
                ['name' => 'Pedagang / Pelaku UMKM Mikro', 'count' => '1.840', 'pct' => '32.8'],
                ['name' => 'Karyawan Swasta & Jasa Komersial', 'count' => '1.420', 'pct' => '25.3'],
                ['name' => 'Aparatur Sipil Negara (ASN / TNI / POLRI)', 'count' => '760', 'pct' => '13.5'],
                ['name' => 'Petani / Buruh Tani / Peternak', 'count' => '620', 'pct' => '11.0'],
                ['name' => 'Lainnya / Sektor Informal Mandiri', 'count' => '970', 'pct' => '17.4'],
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
                ['name' => 'Pembangunan Infrastruktur, Fisik, & Perbaikan Sanitasi Lingkungan', 'amount' => '652.500.000', 'pct' => '45', 'desc' => 'Pavingisasi jalan gang, normalisasi selokan RW 02-05, dan lampu penerangan jalan umum (PJU).'],
                ['name' => 'Pemberdayaan Masyarakat, Kesejahteraan Sosial, & UMKM', 'amount' => '435.000.000', 'pct' => '30', 'desc' => 'Pelatihan digital marketing wirausaha muda, subsidi posyandu balita & lansia, serta bantuan bibit pekarangan.'],
                ['name' => 'Operasional Penyelenggaraan Pelayanan & Administrasi Kantor', 'amount' => '362.500.000', 'pct' => '25', 'desc' => 'Infrastruktur server pelayanan mandiri digital, alat tulis kantor, pemeliharaan gedung, dan honor kebersihan.'],
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
        return view('admin.beranda.sotk', compact('profile'));
    }

    public function visiMisiSejarah()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.visi-misi-sejarah', compact('profile'));
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

    public function transparansi()
    {
        $profile = self::getProfileData();
        return view('admin.beranda.transparansi', compact('profile'));
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
        $existingData = self::getProfileData();
        $section = $request->input('section');
        $statusMsg = 'Profil kelurahan berhasil diperbarui.';

        if ($section === 'identitas_sambutan') {
            $existingData['village_name'] = $request->input('village_name', '');
            $existingData['head_name'] = $request->input('head_name', '');
            $existingData['head_nip'] = $request->input('head_nip', '');
            $existingData['welcome_title'] = $request->input('welcome_title', '');
            $existingData['welcome_text'] = $request->input('welcome_text', '');

            if ($request->hasFile('head_photo')) {
                if (!empty($existingData['head_photo']) && Storage::disk('public')->exists($existingData['head_photo'])) {
                    Storage::disk('public')->delete($existingData['head_photo']);
                }
                $existingData['head_photo'] = $request->file('head_photo')->store('profile', 'public');
            }
            $statusMsg = 'Identitas Kelurahan & Sambutan berhasil diperbarui.';

        } elseif ($section === 'sotk') {
            $existingData['sekel_name'] = $request->input('sekel_name', '');
            $existingData['kasi_pem_name'] = $request->input('kasi_pem_name', '');
            $existingData['kasi_kesra_name'] = $request->input('kasi_kesra_name', '');
            $existingData['kasi_ekbang_name'] = $request->input('kasi_ekbang_name', '');

            $existingData['lurah_tupoksi'] = $request->input('lurah_tupoksi', '');
            $existingData['sekel_tupoksi'] = $request->input('sekel_tupoksi', '');
            $existingData['kasi_pem_tupoksi'] = $request->input('kasi_pem_tupoksi', '');
            $existingData['kasi_kesra_tupoksi'] = $request->input('kasi_kesra_tupoksi', '');
            $existingData['kasi_ekbang_tupoksi'] = $request->input('kasi_ekbang_tupoksi', '');

            $sotkKeys = ['sekel_photo', 'kasi_pem_photo', 'kasi_kesra_photo', 'kasi_ekbang_photo'];
            foreach ($sotkKeys as $photoKey) {
                if ($request->hasFile($photoKey)) {
                    if (!empty($existingData[$photoKey]) && Storage::disk('public')->exists($existingData[$photoKey])) {
                        Storage::disk('public')->delete($existingData[$photoKey]);
                    }
                    $existingData[$photoKey] = $request->file($photoKey)->store('profile', 'public');
                }
            }
            $statusMsg = 'Struktur Organisasi (SOTK) berhasil diperbarui.';

        } elseif ($section === 'visi_misi_sejarah') {
            $existingData['vision'] = $request->input('vision', '');
            $existingData['mission'] = $request->input('mission', '');
            $existingData['history_text'] = $request->input('history_text', '');

            if ($request->hasFile('history_hero_image')) {
                if (!empty($existingData['history_hero_image']) && Storage::disk('public')->exists($existingData['history_hero_image'])) {
                    Storage::disk('public')->delete($existingData['history_hero_image']);
                }
                $existingData['history_hero_image'] = $request->file('history_hero_image')->store('profile', 'public');
            }
            $statusMsg = 'Visi, Misi, dan Sejarah berhasil diperbarui.';

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
            $existingData['footer_description'] = $request->input('footer_description', '');
            $existingData['social_instagram'] = $request->input('social_instagram', '');
            $existingData['social_youtube'] = $request->input('social_youtube', '');
            $existingData['social_tiktok'] = $request->input('social_tiktok', '');
            
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
            
            $totalStr = $request->input('demo_total', '0');
            $totalNum = (float) str_replace(['.', ','], ['', '.'], $totalStr);
            $totalNumActual = $totalNum > 0 ? $totalNum : 1;

            $maleStr = $request->input('demo_male', '0');
            $maleNum = (float) str_replace(['.', ','], ['', '.'], $maleStr);
            
            $femaleStr = $request->input('demo_female', '0');
            $femaleNum = (float) str_replace(['.', ','], ['', '.'], $femaleStr);

            if (($maleNum + $femaleNum) > $totalNum) {
                return back()->withErrors(['Jumlah Laki-laki dan Perempuan tidak boleh melebihi Total Populasi Penduduk.'])->withInput();
            }

            $demographics['total'] = $totalStr;
            $demographics['male'] = $request->input('demo_male', '');
            $demographics['female'] = $request->input('demo_female', '');

            $prodStr = $request->input('demo_prod_count', '0');
            $prodNum = (float) str_replace(['.', ','], ['', '.'], $prodStr);
            $demographics['productive_count'] = $prodStr;
            $demographics['productive_pct'] = str_replace('.', ',', (string)round(($prodNum / $totalNumActual) * 100, 1));

            $childStr = $request->input('demo_child_count', '0');
            $childNum = (float) str_replace(['.', ','], ['', '.'], $childStr);
            $demographics['child_count'] = $childStr;
            $demographics['child_pct'] = str_replace('.', ',', (string)round(($childNum / $totalNumActual) * 100, 1));

            $eldStr = $request->input('demo_elderly_count', '0');
            $eldNum = (float) str_replace(['.', ','], ['', '.'], $eldStr);
            $demographics['elderly_count'] = $eldStr;
            $demographics['elderly_pct'] = str_replace('.', ',', (string)round(($eldNum / $totalNumActual) * 100, 1));

            $demographics['density'] = $request->input('demo_density', '');
            $demographics['avg_family_size'] = $request->input('demo_avg_family', '');

            $occNames = $request->input('occ_name', []);
            $occCounts = $request->input('occ_count', []);
            $occupations = [];
            foreach ($occNames as $i => $name) {
                if (!empty($name)) {
                    $cStr = $occCounts[$i] ?? '0';
                    $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);
                    $occupations[] = [
                        'name' => $name,
                        'count' => $cStr,
                        'pct' => str_replace('.', ',', (string)round(($cNum / $totalNumActual) * 100, 1))
                    ];
                }
            }
            $demographics['occupations'] = $occupations;

            $eduNames = $request->input('edu_name', []);
            $eduCounts = $request->input('edu_count', []);
            $educations = [];
            foreach ($eduNames as $i => $name) {
                if (!empty($name)) {
                    $cStr = $eduCounts[$i] ?? '0';
                    $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);
                    $educations[] = [
                        'name' => $name,
                        'count' => $cStr,
                        'pct' => str_replace('.', ',', (string)round(($cNum / $totalNumActual) * 100, 1))
                    ];
                }
            }
            $demographics['educations'] = $educations;

            $existingData['demographics'] = $demographics;
            $statusMsg = 'Data Demografi Lengkap berhasil diperbarui.';

        } elseif ($section === 'kemitraan') {
            $kemitraanData = [];
            $titles = $request->input('kemitraan_title', []);
            $urls = $request->input('kemitraan_url', []);
            
            foreach ($titles as $index => $title) {
                if (!empty($title)) {
                    $kemitraanData[] = [
                        'title' => $title,
                        'url' => $urls[$index] ?? '#'
                    ];
                }
            }
            $existingData['kemitraan'] = $kemitraanData;
            $statusMsg = 'Daftar Kemitraan Instansi berhasil diperbarui.';

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
            $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
            $apbd['year'] = $request->input('apbd_year', '');
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
            $apbd['allocations'] = $allocations;
            $existingData['apbd'] = $apbd;
            $statusMsg = 'Transparansi APBD berhasil diperbarui.';
        }

        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', $statusMsg);
    }

    public function storeApbd(Request $request)
    {
        $existingData = self::getProfileData();
        $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
        $allocations = $apbd['allocations'] ?? [];

        $allocations[] = [
            'name' => $request->input('apbd_alloc_name'),
            'amount' => $request->input('apbd_alloc_amount'),
            'pct' => $request->input('apbd_alloc_pct'),
            'desc' => $request->input('apbd_alloc_desc', '')
        ];

        $apbd['allocations'] = $allocations;
        $existingData['apbd'] = $apbd;
        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('success', 'Bidang alokasi APBD baru berhasil ditambahkan.');
    }

    public function updateApbd(Request $request, $index)
    {
        $existingData = self::getProfileData();
        $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
        $allocations = $apbd['allocations'] ?? [];

        if (isset($allocations[$index])) {
            $allocations[$index] = [
                'name' => $request->input('apbd_alloc_name'),
                'amount' => $request->input('apbd_alloc_amount'),
                'pct' => $request->input('apbd_alloc_pct'),
                'desc' => $request->input('apbd_alloc_desc', '')
            ];

            $apbd['allocations'] = $allocations;
            $existingData['apbd'] = $apbd;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Data bidang alokasi berhasil diperbarui.');
        }

        return back()->withErrors(['Bidang alokasi tidak ditemukan.']);
    }

    public function destroyApbd($index)
    {
        $existingData = self::getProfileData();
        $apbd = $existingData['apbd'] ?? self::getDefaultApbd();
        $allocations = $apbd['allocations'] ?? [];

        if (isset($allocations[$index])) {
            array_splice($allocations, $index, 1);
            $apbd['allocations'] = $allocations;
            $existingData['apbd'] = $apbd;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Bidang alokasi berhasil dihapus.');
        }

        return back()->withErrors(['Bidang alokasi tidak ditemukan.']);
    }

    public function storeStatistik(Request $request, $type)
    {
        $existingData = self::getProfileData();
        $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();
        $key = $type === 'education' ? 'educations' : 'occupations';
        $items = $demographics[$key] ?? [];

        $totalStr = $demographics['total'] ?? '1';
        $totalNum = (float) str_replace(['.', ','], ['', '.'], $totalStr);
        $totalNum = $totalNum > 0 ? $totalNum : 1;

        $cStr = $request->input('stat_count', '0');
        $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);

        $items[] = [
            'name' => $request->input('stat_name'),
            'count' => $cStr,
            'pct' => str_replace('.', ',', (string)round(($cNum / $totalNum) * 100, 1))
        ];

        $demographics[$key] = $items;
        $existingData['demographics'] = $demographics;
        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('success', 'Data statistik berhasil ditambahkan.');
    }

    public function updateStatistik(Request $request, $type, $index)
    {
        $existingData = self::getProfileData();
        $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();
        $key = $type === 'education' ? 'educations' : 'occupations';
        $items = $demographics[$key] ?? [];

        if (isset($items[$index])) {
            $totalStr = $demographics['total'] ?? '1';
            $totalNum = (float) str_replace(['.', ','], ['', '.'], $totalStr);
            $totalNum = $totalNum > 0 ? $totalNum : 1;

            $cStr = $request->input('stat_count', '0');
            $cNum = (float) str_replace(['.', ','], ['', '.'], $cStr);

            $items[$index] = [
                'name' => $request->input('stat_name'),
                'count' => $cStr,
                'pct' => str_replace('.', ',', (string)round(($cNum / $totalNum) * 100, 1))
            ];

            $demographics[$key] = $items;
            $existingData['demographics'] = $demographics;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Data statistik berhasil diperbarui.');
        }

        return back()->withErrors(['Data tidak ditemukan.']);
    }

    public function destroyStatistik($type, $index)
    {
        $existingData = self::getProfileData();
        $demographics = $existingData['demographics'] ?? self::getDefaultDemographics();
        $key = $type === 'education' ? 'educations' : 'occupations';
        $items = $demographics[$key] ?? [];

        if (isset($items[$index])) {
            array_splice($items, $index, 1);
            $demographics[$key] = $items;
            $existingData['demographics'] = $demographics;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Data statistik berhasil dihapus.');
        }

        return back()->withErrors(['Data tidak ditemukan.']);
    }
}
