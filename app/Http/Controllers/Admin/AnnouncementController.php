<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Tampilkan daftar Pengumuman & Running Text.
     */
    public function index(Request $request)
    {
        $query = Announcement::with('category')->latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->input('status') === 'urgent') {
                $query->where('is_urgent', true);
            }
        }

        $totalCount = Announcement::count();
        $activeCount = Announcement::where('is_active', true)->count();
        $urgentCount = Announcement::where('is_urgent', true)->count();
        $activeMarquee = Announcement::where('is_active', true)->latest()->get();
        $announcements = $query->paginate(10)->withQueryString();
        $categories = \App\Models\Category::orderByRaw("CASE WHEN type = 'pengumuman' THEN 0 ELSE 1 END")
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.pengumuman.index', compact('announcements', 'categories', 'totalCount', 'activeCount', 'urgentCount', 'activeMarquee'));
    }

    /**
     * Simpan pengumuman / running text baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'custom_category' => 'nullable|string|max:100',
            'content' => 'required|string',
            'badge_type' => 'required|string|max:50',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'content.required' => 'Isi pengumuman / running text wajib diisi.',
        ]);

        $categoryId = \App\Models\Category::resolveId($request->input('category_id'), $request->input('custom_category'), 'pengumuman');
        if (!$categoryId) {
            return back()->withErrors(['category_id' => 'Kategori pengumuman wajib dipilih atau diisi.'])->withInput();
        }

        Announcement::create([
            'title' => $request->input('title'),
            'category_id' => $categoryId,
            'content' => $request->input('content'),
            'badge_type' => strtolower($request->input('badge_type', 'info')),
            'link_url' => $request->input('link_url'),
            'is_active' => $request->boolean('is_active', true),
            'is_urgent' => $request->boolean('is_urgent'),
        ]);

        return back()->with('status', 'Pengumuman / Teks Berjalan baru berhasil ditambahkan.');
    }

    /**
     * Perbarui data pengumuman.
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'custom_category' => 'nullable|string|max:100',
            'content' => 'required|string',
            'badge_type' => 'required|string|max:50',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
        ]);

        $categoryId = \App\Models\Category::resolveId($request->input('category_id'), $request->input('custom_category'), 'pengumuman');
        if (!$categoryId) {
            return back()->withErrors(['category_id' => 'Kategori pengumuman wajib dipilih atau diisi.'])->withInput();
        }

        $announcement->update([
            'title' => $request->input('title'),
            'category_id' => $categoryId,
            'content' => $request->input('content'),
            'badge_type' => strtolower($request->input('badge_type', 'info')),
            'link_url' => $request->input('link_url'),
            'is_active' => $request->boolean('is_active'),
            'is_urgent' => $request->boolean('is_urgent'),
        ]);

        return back()->with('status', "Pengumuman {$announcement->title} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif pengumuman.
     */
    public function toggle($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'is_active' => !$announcement->is_active,
        ]);

        $statusText = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Pengumuman {$announcement->title} berhasil {$statusText}.");
    }

    /**
     * Hapus pengumuman.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return back()->with('status', 'Pengumuman berhasil dihapus.');
    }
}
