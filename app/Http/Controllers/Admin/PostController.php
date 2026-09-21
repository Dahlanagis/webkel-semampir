<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Tampilkan daftar Berita & Artikel Kelurahan.
     */
    public function index(Request $request)
    {
        $query = Post::with('category')->latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->input('category_id') !== 'all') {
            $query->where('category_id', $request->input('category_id'));
        }

        $posts = $query->paginate(10)->withQueryString();
        $categories = Category::where('type', 'berita')->get();

        return view('admin.berita.index', compact('posts', 'categories'));
    }



    /**
     * Simpan berita & artikel baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'image_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'is_slider' => 'boolean',
            'published_at' => 'nullable|date',
        ], [
            'title.required' => 'Judul berita wajib diisi.',
            'category_id.required' => 'Kategori berita wajib dipilih.',
            'content.required' => 'Isi berita wajib diisi.',
            'image_file.image' => 'Foto sampul harus berupa file foto.',
            'image_file.mimes' => 'Foto sampul harus berformat JPG, JPEG, PNG, atau WEBP.',
            'image_file.max' => 'Ukuran foto unggulan maksimal 3MB.',
            'image_url.url' => 'Format link URL gambar tidak valid.',
        ]);

        if ($request->boolean('is_featured')) {
            $currentCount = Post::where('is_featured', true)->count();
            if ($currentCount >= 2) {
                return back()->withErrors(['is_featured' => 'Gagal mempublikasikan: Maksimal 2 berita yang dapat dijadikan Berita Utama di beranda. Silakan nonaktifkan yang lain terlebih dahulu.'])->withInput();
            }
        }

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('posts', 'public');
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        Post::create([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')) . '-' . Str::random(5),
            'category_id' => $request->input('category_id'),
            'author' => auth()->user()->name ?? 'Admin Kelurahan',
            'excerpt' => $request->input('excerpt') ?? Str::limit(strip_tags($request->input('content')), 150),
            'content' => $request->input('content'),
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'is_slider' => $request->boolean('is_slider'),
            'published_at' => $request->input('published_at') ?? now(),
        ]);

        return back()->with('status', 'Berita / Artikel baru berhasil dipublikasikan.');
    }



    /**
     * Perbarui data berita & artikel.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'image_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'is_slider' => 'boolean',
            'published_at' => 'nullable|date',
        ], [
            'title.required' => 'Judul berita wajib diisi.',
            'category_id.required' => 'Kategori berita wajib dipilih.',
            'content.required' => 'Isi berita wajib diisi.',
            'image_file.image' => 'Foto sampul harus berupa file foto.',
            'image_file.mimes' => 'Foto sampul harus berformat JPG, JPEG, PNG, atau WEBP.',
            'image_file.max' => 'Ukuran foto unggulan maksimal 3MB.',
            'image_url.url' => 'Format link URL gambar tidak valid.',
        ]);

        if ($request->boolean('is_featured') && !$post->is_featured) {
            $currentCount = Post::where('is_featured', true)->count();
            if ($currentCount >= 2) {
                return back()->withErrors(['is_featured' => 'Gagal memperbarui: Maksimal 2 berita yang dapat dijadikan Berita Utama di beranda. Silakan nonaktifkan yang lain terlebih dahulu.'])->withInput();
            }
        }

        $imagePath = $post->image;
        if ($request->hasFile('image_file')) {
            if ($post->image && !str_starts_with($post->image, 'http') && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image_file')->store('posts', 'public');
        } elseif ($request->filled('image_url')) {
            if ($post->image && !str_starts_with($post->image, 'http') && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->input('image_url');
        }

        $post->update([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')) . '-' . $post->id,
            'category_id' => $request->input('category_id'),
            'excerpt' => $request->input('excerpt') ?? Str::limit(strip_tags($request->input('content')), 150),
            'content' => $request->input('content'),
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'is_slider' => $request->boolean('is_slider'),
            'published_at' => $request->input('published_at') ?? $post->published_at,
        ]);

        return back()->with('status', "Berita {$post->title} berhasil diperbarui.");
    }

    /**
     * Hapus berita.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return back()->with('status', 'Berita / Artikel berhasil dihapus.');
    }

    /**
     * Toggle status Berita Utama di beranda
     */
    public function toggleFeatured($id)
    {
        $post = Post::findOrFail($id);

        if (!$post->is_featured) {
            $currentCount = Post::where('is_featured', true)->count();
            if ($currentCount >= 2) {
                return back()->with('error', 'Maksimal 2 berita yang dapat dijadikan Berita Utama di beranda. Silakan hapus/nonaktifkan berita utama lain terlebih dahulu.');
            }
        }

        $post->is_featured = !$post->is_featured;
        $post->save();

        return back()->with('status', 'Status Berita Utama berhasil diubah.');
    }
}
