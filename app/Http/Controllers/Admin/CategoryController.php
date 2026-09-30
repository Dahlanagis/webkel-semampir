<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori (bisa difilter berdasarkan type).
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 'berita'); // Default tab is berita

        $relationMap = [
            'berita' => 'posts',
            'galeri' => 'galleries',
            'pengumuman' => 'announcements',
            'dokumen' => 'documents',
            'agenda' => 'agendas'
        ];
        $relation = $relationMap[$type] ?? 'posts';

        $categories = Category::where('type', $type)->withCount($relation)->orderBy('name', 'asc')->get();

        return view('admin.kategori.index', compact('categories', 'type'));
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:berita,galeri,pengumuman,dokumen,agenda',
            'description' => 'nullable|string|max:500',
            'color_code' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Tipe kategori wajib dipilih.',
        ]);

        $slug = Str::slug($request->input('name'));
        
        // Ensure slug is unique per type
        if (Category::where('type', $request->input('type'))->where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $category = Category::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'color_code' => $request->input('color_code') ?? 'slate',
        ]);

        // No auto-sync needed for categories anymore

        ActivityLog::record('CREATE', "Menambahkan kategori informasi baru: {$category->name} ({$category->type})");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => "Kategori '{$category->name}' berhasil ditambahkan."
            ]);
        }

        return back()->with('status', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data kategori.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'color_code' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        $slug = Str::slug($request->input('name'));
        
        if (Category::where('type', $category->type)->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $slug . '-' . time();
        }

        $oldName = $category->name;

        $category->update([
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'color_code' => $request->input('color_code') ?? 'slate',
        ]);

        // No auto-sync needed for categories anymore

        ActivityLog::record('UPDATE', "Memperbarui kategori informasi: {$category->name}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => "Kategori '{$category->name}' berhasil diperbarui."
            ]);
        }

        return back()->with('status', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Hapus kategori.
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        // Prevent deletion if associated items exist
        $relationMap = [
            'berita' => 'posts',
            'galeri' => 'galleries',
            'pengumuman' => 'announcements',
            'dokumen' => 'documents'
        ];
        $relation = $relationMap[$category->type] ?? 'posts';
        $count = $category->{$relation}()->count();

        if ($count > 0) {
            $typeNames = [
                'berita' => 'Berita/Artikel',
                'galeri' => 'Galeri Foto',
                'pengumuman' => 'Pengumuman',
                'dokumen' => 'Dokumen Publik'
            ];
            $typeName = $typeNames[$category->type] ?? 'data';
            $msg = "Kategori '{$category->name}' tidak bisa dihapus karena saat ini sedang digunakan oleh {$count} data {$typeName}. Silakan hapus atau pindahkan data {$typeName} tersebut terlebih dahulu.";
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], 422);
            }
            
            return back()->with('error', $msg);
        }

        $categoryName = $category->name;
        $categoryType = $category->type;
        $category->delete();

        // No auto-sync needed for categories anymore

        ActivityLog::record('DELETE', "Menghapus kategori informasi: {$categoryName}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori '{$categoryName}' berhasil dihapus."
            ]);
        }

        return back()->with('status', "Kategori '{$categoryName}' berhasil dihapus.");
    }
}
