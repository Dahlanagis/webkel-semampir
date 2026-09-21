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
        $query = Announcement::latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
        }

        $announcements = $query->paginate(10)->withQueryString();
        $categories = \App\Models\Category::where('type', 'pengumuman')->get();

        return view('admin.pengumuman.index', compact('announcements', 'categories'));
    }

    /**
     * Simpan pengumuman / running text baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'badge_type' => 'required|in:info,warning,danger',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'category_id.required' => 'Kategori pengumuman wajib dipilih.',
            'content.required' => 'Isi pengumuman / running text wajib diisi.',
        ]);

        Announcement::create([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
            'badge_type' => $request->input('badge_type'),
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
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'badge_type' => 'required|in:info,warning,danger',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
        ]);

        $announcement->update([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
            'badge_type' => $request->input('badge_type'),
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
