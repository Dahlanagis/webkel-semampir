<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Tampilkan daftar Galeri Kegiatan Kelurahan.
     */
    public function index(Request $request)
    {
        $query = Gallery::with(['categoryModel', 'images'])->latest();

        if ($request->filled('category_id') && $request->input('category_id') !== 'all') {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('type') && in_array($request->input('type'), ['foto', 'video'])) {
            $query->where('type', $request->input('type'));
        }

        $galleries = $query->paginate(12)->withQueryString();
        $categories = \App\Models\Category::where('type', 'galeri')->get();

        return view('admin.galeri.index', compact('galleries', 'categories'));
    }

    /**
     * Unggah foto dokumentasi kegiatan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'caption' => 'nullable|string|max:500',
            'type' => 'required|in:foto,video',
            'image_files' => 'nullable|array|max:10', // Allow up to 10 photos per upload
            'image_files.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url' => 'nullable|url',
            'youtube_url' => 'nullable|required_if:type,video|url',
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'category_id.required' => 'Kategori kegiatan wajib dipilih.',
            'type.required' => 'Tipe media wajib dipilih.',
            'image_files.array' => 'Format file tidak valid.',
            'image_files.max' => 'Maksimal upload 10 foto sekaligus.',
            'image_files.*.image' => 'File harus berupa foto.',
            'image_files.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'image_files.*.max' => 'Ukuran setiap foto maksimal 5MB.',
            'image_url.url' => 'Format link URL gambar tidak valid.',
            'youtube_url.required_if' => 'Link YouTube wajib diisi jika tipe media adalah Video.',
            'youtube_url.url' => 'Format link YouTube tidak valid.',
        ]);

        if ($request->input('type') === 'foto' && !$request->hasFile('image_files') && !$request->filled('image_url')) {
            return back()->withErrors(['image_files' => 'Minimal 1 file foto atau Link URL gambar wajib diisi.'])->withInput();
        }

        $imagePath = null;
        $youtubeId = null;
        $additionalImagePaths = [];

        if ($request->input('type') === 'foto') {
            if ($request->hasFile('image_files')) {
                $files = $request->file('image_files');
                // The first file is used as the cover
                $imagePath = $files[0]->store('gallery', 'public');
                
                // Store all files in additional paths
                foreach ($files as $file) {
                    $additionalImagePaths[] = $file->store('gallery', 'public');
                }
            } elseif ($request->filled('image_url')) {
                $imagePath = $request->input('image_url');
            }
        } elseif ($request->input('type') === 'video') {
            // Extract youtube ID
            $url = $request->input('youtube_url');
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
            $youtubeId = $match[1] ?? null;
            
            // Set youtube thumbnail as fallback image
            if ($youtubeId) {
                $imagePath = 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg';
            }
        }

        if ($request->has('show_on_homepage')) {
            $currentCount = Gallery::where('show_on_homepage', true)->count();
            if ($currentCount >= 5) {
                return back()->withErrors(['show_on_homepage' => 'Gagal mengunggah: Maksimal 5 album/video yang dapat ditampilkan di beranda. Silakan nonaktifkan album lain terlebih dahulu.'])->withInput();
            }
        }

        $gallery = Gallery::create([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'caption' => $request->input('caption'),
            'type' => $request->input('type'),
            'youtube_id' => $youtubeId,
            'image' => $imagePath,
            'category' => \App\Models\Category::find($request->input('category_id'))->name ?? 'Kegiatan', // Fallback for legacy
            'show_on_homepage' => $request->has('show_on_homepage'),
        ]);

        if (!empty($additionalImagePaths)) {
            foreach ($additionalImagePaths as $path) {
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path
                ]);
            }
        }

        return back()->with('status', 'Foto dokumentasi kegiatan baru berhasil ditambahkan ke galeri.');
    }

    /**
     * Perbarui data foto galeri kegiatan.
     */
    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'caption' => 'nullable|string|max:500',
            'type' => 'required|in:foto,video',
            'image_files' => 'nullable|array|max:10',
            'image_files.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url' => 'nullable|url',
            'youtube_url' => 'nullable|required_if:type,video|url',
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'category_id.required' => 'Kategori kegiatan wajib dipilih.',
            'type.required' => 'Tipe media wajib dipilih.',
            'image_files.array' => 'Format file tidak valid.',
            'image_files.max' => 'Maksimal upload 10 foto sekaligus.',
            'image_files.*.image' => 'File harus berupa foto.',
            'image_files.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'image_files.*.max' => 'Ukuran setiap foto maksimal 5MB.',
            'image_url.url' => 'Format link URL gambar tidak valid.',
            'youtube_url.required_if' => 'Link YouTube wajib diisi jika tipe media adalah Video.',
            'youtube_url.url' => 'Format link YouTube tidak valid.',
        ]);

        $imagePath = $gallery->image;
        $youtubeId = $gallery->youtube_id;
        $additionalImagePaths = [];

        if ($request->input('type') === 'foto') {
            if ($request->hasFile('image_files')) {
                $files = $request->file('image_files');
                
                // If there's no cover image at all, set the first uploaded file as cover
                if (!$gallery->image) {
                    $imagePath = $files[0]->store('gallery', 'public');
                }
                
                foreach ($files as $file) {
                    $additionalImagePaths[] = $file->store('gallery', 'public');
                }
            } elseif ($request->filled('image_url')) {
                // If using URL, we set it as cover. Existing album images are preserved.
                $imagePath = $request->input('image_url');
            }
            $youtubeId = null; // Clear if it was video before
        } elseif ($request->input('type') === 'video') {
            $url = $request->input('youtube_url');
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
            $newYoutubeId = $match[1] ?? null;

            if ($newYoutubeId) {
                $youtubeId = $newYoutubeId;
                // Delete old local image if any
                if ($gallery->image && !str_starts_with($gallery->image, 'http') && Storage::disk('public')->exists($gallery->image)) {
                    Storage::disk('public')->delete($gallery->image);
                }
                $imagePath = 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg';
            }
        }

        if ($request->has('show_on_homepage') && !$gallery->show_on_homepage) {
            $currentCount = Gallery::where('show_on_homepage', true)->count();
            if ($currentCount >= 5) {
                return back()->withErrors(['show_on_homepage' => 'Gagal memperbarui: Maksimal 5 album/video yang dapat ditampilkan di beranda. Silakan nonaktifkan album lain terlebih dahulu.'])->withInput();
            }
        }

        $gallery->update([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
            'caption' => $request->input('caption'),
            'type' => $request->input('type'),
            'youtube_id' => $youtubeId,
            'image' => $imagePath,
            'category' => \App\Models\Category::find($request->input('category_id'))->name ?? 'Kegiatan', // Fallback for legacy
            'show_on_homepage' => $request->has('show_on_homepage'),
        ]);

        if (!empty($additionalImagePaths)) {
            foreach ($additionalImagePaths as $path) {
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path
                ]);
            }
        }

        return back()->with('status', 'Foto galeri kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus foto satuan dari album galeri.
     */
    public function destroyImage($galleryId, $imageId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $image = GalleryImage::where('gallery_id', $galleryId)->findOrFail($imageId);

        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // If the deleted image was the cover, pick another image as cover if exists
        if ($gallery->image === $image->image_path) {
            $image->delete(); // delete first so it's not picked
            $nextImage = $gallery->images()->first();
            if ($nextImage) {
                $gallery->update(['image' => $nextImage->image_path]);
            } else {
                $gallery->update(['image' => null]);
            }
        } else {
            $image->delete();
        }

        return back()->with('status', 'Foto berhasil dihapus dari album.');
    }

    /**
     * Hapus keseluruhan album/foto dari galeri.
     */
    public function destroy($id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);
        
        // Hapus semua foto tambahan
        foreach ($gallery->images as $img) {
            if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        // Hapus cover
        if ($gallery->image && !str_starts_with($gallery->image, 'http') && Storage::disk('public')->exists($gallery->image)) {
            Storage::disk('public')->delete($gallery->image);
        }
        
        $gallery->delete();

        return back()->with('status', 'Album galeri beserta seluruh fotonya berhasil dihapus.');
    }

    /**
     * Toggle status tampil di beranda
     */
    public function toggleHomepage($id)
    {
        $gallery = Gallery::findOrFail($id);

        if (!$gallery->show_on_homepage) {
            $currentCount = Gallery::where('show_on_homepage', true)->count();
            if ($currentCount >= 5) {
                return back()->with('error', 'Maksimal 5 album atau video yang dapat ditampilkan di beranda. Silakan hapus/nonaktifkan album lain dari beranda terlebih dahulu.');
            }
        }

        $gallery->show_on_homepage = !$gallery->show_on_homepage;
        $gallery->save();

        return back()->with('status', 'Status tampil di beranda berhasil diubah.');
    }
}
