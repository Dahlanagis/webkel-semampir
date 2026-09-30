<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationMenu;
use Illuminate\Http\Request;

class NavigationMenuController extends Controller
{
    public function index()
    {
        $menus = NavigationMenu::orderBy('section')
            ->orderBy('order')
            ->get()
            ->groupBy('section');

        return view('admin.navigation.index', compact('menus'));
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

        return back()->with('success', 'Menu navigasi berhasil diperbarui.');
    }

    public function destroy(NavigationMenu $navigation)
    {
        $protectedUrls = ['/visi-misi', '/sejarah', '/struktur-organisasi'];
        if (in_array($navigation->url, $protectedUrls)) {
            return back()->with('error', 'Menu utama profil sistem tidak dapat dihapus.');
        }

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
        return back()->with('success', 'Menu navigasi berhasil dihapus.');
    }
}
