<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Tampilkan daftar Layanan Publik Beranda.
     */
    public function index(Request $request)
    {
        $query = Service::orderBy('order', 'asc');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $services = $query->paginate(10)->withQueryString();

        return view('admin.layanan-publik.index', compact('services'));
    }

    /**
     * Simpan layanan publik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'action_url' => 'nullable|string|max:255',
            'badge_label' => 'nullable|string|max:50',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        Service::create([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'action_url' => $request->input('action_url'),
            'badge_label' => $request->input('badge_label'),
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', 'Layanan publik baru berhasil ditambahkan.');
    }

    /**
     * Perbarui data layanan publik.
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'action_url' => 'nullable|string|max:255',
            'badge_label' => 'nullable|string|max:50',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $service->update([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'action_url' => $request->input('action_url'),
            'badge_label' => $request->input('badge_label'),
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', "Layanan publik {$service->title} berhasil diperbarui.");
    }

    /**
     * Aktifkan atau nonaktifkan layanan publik.
     */
    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->update([
            'is_active' => !$service->is_active,
        ]);

        $statusText = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Layanan publik {$service->title} berhasil {$statusText}.");
    }

    /**
     * Hapus layanan publik.
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        
        return back()->with('status', "Layanan publik {$service->title} berhasil dihapus.");
    }
}
