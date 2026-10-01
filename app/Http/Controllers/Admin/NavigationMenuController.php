<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NavigationMenu;
use Illuminate\Http\Request;

class NavigationMenuController extends Controller
{
    public function index()
    {
        if (NavigationMenu::count() === 0) {
            self::seedDefaultMenus();
        }

        $menus = NavigationMenu::orderBy('section')
            ->orderBy('order')
            ->get()
            ->groupBy('section');

        return view('admin.navigation.index', compact('menus'));
    }

    /**
     * Seed tautan menu default untuk profil, layanan, dokumen, dan informasi.
     */
    public static function seedDefaultMenus(): void
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

    public function store(Request $request)
    {
        $request->validate([
            'section' => 'required|in:profil,layanan,dokumen,informasi',
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'order' => 'required|integer',
        ]);

        $url = $request->url;

        if (empty($url)) {
            if ($request->section === 'dokumen') {
                $document = \App\Models\Document::create([
                    'name' => $request->title,
                    'is_active' => $request->boolean('is_active'),
                ]);
                $url = '/dokumen?id=' . $document->id;
            } elseif ($request->section === 'profil') {
                $page = \App\Models\Page::create([
                    'title' => $request->title,
                    'is_active' => $request->boolean('is_active'),
                ]);
                $url = '/halaman/' . $page->slug;
            } elseif ($request->section === 'layanan') {
                $service = \App\Models\ServiceType::create([
                    'name' => $request->title,
                    'slug' => \Illuminate\Support\Str::slug($request->title),
                    'is_active' => $request->boolean('is_active'),
                ]);
                $url = '/standar-pelayanan?id=' . $service->id;
            }
        }

        NavigationMenu::updateOrCreate(
            [
                'section' => $request->section,
                'url' => $url ?? '#',
            ],
            [
                'title' => $request->title,
                'order' => $request->order,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        ActivityLog::record('CREATE', "Menambahkan menu navigasi: {$request->title} (Menu: " . strtoupper($request->section) . ")");

        return back()->with('success', 'Menu navigasi berhasil ditambahkan.');
    }

    public function update(Request $request, NavigationMenu $navigation)
    {
        $request->validate([
            'section' => 'required|in:profil,layanan,dokumen,informasi',
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'order' => 'required|integer',
        ]);

        $oldTitle = $navigation->title;
        $url = $request->filled('url') ? $request->url : $navigation->url;

        $navigation->update([
            'section' => $request->section,
            'title' => $request->title,
            'url' => $url,
            'order' => $request->order,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->section === 'dokumen' && $oldTitle !== $request->title) {
            if (preg_match('/id=(\d+)/', $navigation->url, $matches)) {
                $doc = \App\Models\Document::find($matches[1]);
                if ($doc) {
                    $doc->update([
                        'name' => $request->title,
                        'is_active' => $request->boolean('is_active'),
                    ]);
                }
            }
        } elseif ($request->section === 'profil' && $oldTitle !== $request->title) {
            if (preg_match('/^\/halaman\/(.+)$/', $navigation->url, $matches)) {
                $page = \App\Models\Page::where('slug', $matches[1])->first();
                if ($page) {
                    $page->update([
                        'title' => $request->title,
                        'is_active' => $request->boolean('is_active'),
                    ]);
                    $navigation->update(['url' => '/halaman/' . $page->slug]);
                }
            }
        } elseif ($request->section === 'layanan' && $oldTitle !== $request->title) {
            if (preg_match('/id=(\d+)/', $navigation->url, $matches)) {
                $service = \App\Models\ServiceType::find($matches[1]);
                if ($service) {
                    $service->update([
                        'name' => $request->title,
                        'is_active' => $request->boolean('is_active'),
                    ]);
                }
            }
        }

        ActivityLog::record('UPDATE', "Memperbarui menu navigasi: {$request->title} (Menu: " . strtoupper($request->section) . ")");

        return back()->with('success', 'Menu navigasi berhasil diperbarui.');
    }

    public function destroy(NavigationMenu $navigation)
    {
        $protectedUrls = ['/visi-misi', '/sejarah', '/struktur-organisasi'];
        if (in_array($navigation->url, $protectedUrls)) {
            return back()->with('error', 'Menu utama profil sistem tidak dapat dihapus.');
        }

        $title = $navigation->title;
        $section = $navigation->section;

        if ($navigation->section === 'dokumen') {
            if (preg_match('/id=(\d+)/', $navigation->url, $matches)) {
                $doc = \App\Models\Document::find($matches[1]);
                if ($doc) {
                    $doc->delete();
                }
            }
        } elseif ($navigation->section === 'profil') {
            if (preg_match('/^\/halaman\/(.+)$/', $navigation->url, $matches)) {
                $page = \App\Models\Page::where('slug', $matches[1])->first();
                if ($page) {
                    $page->delete();
                }
            }
        } elseif ($navigation->section === 'layanan') {
            if (preg_match('/id=(\d+)/', $navigation->url, $matches)) {
                $service = \App\Models\ServiceType::find($matches[1]);
                if ($service) {
                    $service->delete();
                }
            }
        }
        $navigation->delete();

        ActivityLog::record('DELETE', "Menghapus menu navigasi: {$title} (Menu: " . strtoupper($section) . ")");

        return back()->with('success', 'Menu navigasi berhasil dihapus.');
    }
}
