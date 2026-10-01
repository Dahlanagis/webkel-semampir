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
        $categories = \App\Models\Category::orderByRaw("CASE WHEN type = 'galeri' THEN 0 ELSE 1 END")
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.galeri.index', compact('galleries', 'categories'));
    }

    /**
     * Unggah foto dokumentasi kegiatan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'custom_category' => 'nullable|string|max:100',
            'caption' => 'nullable|string|max:500',
            'type' => 'required|in:foto,video',
            'image_files' => 'nullable|array|max:10', // Allow up to 10 photos per upload
            'image_files.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url' => 'nullable|url',
            'youtube_url' => 'nullable|required_if:type,video|url',
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'type.required' => 'Tipe media wajib dipilih.',
            'image_files.array' => 'Format file tidak valid.',
            'image_files.max' => 'Maksimal upload 10 foto sekaligus.',
            'image_files.*.image' => 'File ditolak! Berkas harus berupa foto atau gambar.',
            'image_files.*.mimes' => 'File ditolak! Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'image_files.*.max' => 'File ditolak! Ukuran setiap foto maksimal 5 MB.',
            'image_url.url' => 'Format link URL gambar tidak valid.',
            'youtube_url.required_if' => 'Link YouTube wajib diisi jika tipe media adalah Video.',
            'youtube_url.url' => 'Format link YouTube tidak valid.',
        ]);

        $categoryId = \App\Models\Category::resolveId($request->input('category_id'), $request->input('custom_category'), 'galeri');
        if (!$categoryId) {
            return back()->withErrors(['category_id' => 'Kategori kegiatan wajib dipilih atau diisi.'])->withInput();
        }

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



        $catModel = \App\Models\Category::find($categoryId);
        $gallery = Gallery::create([
            'title' => $request->input('title'),
            'category_id' => $categoryId,
            'caption' => $request->input('caption'),
            'type' => $request->input('type'),
            'youtube_id' => $youtubeId,
            'image' => $imagePath,
            'category' => $catModel->name ?? 'Kegiatan', // Fallback for legacy
            'show_on_homepage' => $request->has('show_on_homepage'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
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
     * Hapus berkas fisik gambar dari folder penyimpanan lokal / server.
     * Mencegah orphan files di storage/app/public, public/storage, maupun public/uploads.
     */
    public static function deletePhysicalFile(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // Abaikan URL eksternal (Unsplash, YouTube thumbnails, dsb.)
        if (preg_match('/^https?:\/\//i', $path)) {
            return false;
        }

        $deleted = false;
        $clean = ltrim($path, '/\\');

        // Bersihkan awalan storage/ atau public/
        if (str_starts_with($clean, 'storage/')) {
            $storageSub = substr($clean, 8);
        } elseif (str_starts_with($clean, 'public/')) {
            $storageSub = substr($clean, 7);
        } else {
            $storageSub = $clean;
        }

        // 1. Hapus via Laravel Storage disk public
        try {
            if (Storage::disk('public')->exists($storageSub)) {
                $deleted = Storage::disk('public')->delete($storageSub) || $deleted;
            }
        } catch (\Throwable $e) {}

        // 2. Hapus langsung dari direktori storage/app/public/...
        $storageAppPath = storage_path('app/public/' . $storageSub);
        if (file_exists($storageAppPath) && is_file($storageAppPath)) {
            @unlink($storageAppPath);
            $deleted = true;
        }

        // 3. Hapus langsung dari symlink / target public/storage/...
        $publicStoragePath = public_path('storage/' . $storageSub);
        if (file_exists($publicStoragePath) && is_file($publicStoragePath)) {
            @unlink($publicStoragePath);
            $deleted = true;
        }

        // 4. Hapus langsung dari public_path(...) jika tersimpan di public/uploads dsb.
        $directPublicPath = public_path($clean);
        if (file_exists($directPublicPath) && is_file($directPublicPath)) {
            @unlink($directPublicPath);
            $deleted = true;
        }

        return $deleted;
    }

    /**
     * Perbarui data foto / video galeri kegiatan.
     */
    public function update(Request $request, $id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'custom_category' => 'nullable|string|max:100',
            'caption' => 'nullable|string|max:500',
            'type' => 'required|in:foto,video',
            'created_at' => 'nullable|date',
            'cover_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_files' => 'nullable|array|max:10',
            'image_files.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url' => 'nullable|url',
            'youtube_url' => 'nullable|required_if:type,video|url',
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'type.required' => 'Tipe media wajib dipilih.',
            'cover_file.image' => 'File foto sampul harus berupa berkas gambar.',
            'cover_file.mimes' => 'Format foto sampul harus JPG, JPEG, PNG, atau WEBP.',
            'cover_file.max' => 'Ukuran foto sampul maksimal 5 MB.',
            'image_files.array' => 'Format file album tidak valid.',
            'image_files.max' => 'Maksimal upload 10 foto album sekaligus.',
            'image_files.*.image' => 'File album harus berupa gambar.',
            'image_files.*.mimes' => 'Format foto album harus JPG, JPEG, PNG, atau WEBP.',
            'image_files.*.max' => 'Ukuran setiap foto album maksimal 5 MB.',
            'image_url.url' => 'Format link URL gambar tidak valid.',
            'youtube_url.required_if' => 'Link YouTube wajib diisi jika tipe media adalah Video.',
            'youtube_url.url' => 'Format link YouTube tidak valid.',
        ]);

        $categoryId = \App\Models\Category::resolveId($request->input('category_id'), $request->input('custom_category'), 'galeri');
        if (!$categoryId) {
            return back()->withErrors(['category_id' => 'Kategori kegiatan wajib dipilih atau diisi.'])->withInput();
        }

        $imagePath = $gallery->image;
        $youtubeId = $gallery->youtube_id;
        $additionalImagePaths = [];

        if ($request->input('type') === 'foto') {
            // 1. Jika ada upload foto sampul baru: HAPUS file fisik lama, simpan file baru
            if ($request->hasFile('cover_file')) {
                self::deletePhysicalFile($gallery->image);
                $imagePath = $request->file('cover_file')->store('gallery', 'public');
            } elseif ($request->filled('image_url')) {
                // Jika diganti lewat URL gambar
                self::deletePhysicalFile($gallery->image);
                $imagePath = $request->input('image_url');
            }

            // 2. Jika ada penambahan foto ke dalam album
            if ($request->hasFile('image_files')) {
                $files = $request->file('image_files');
                
                // Jika belum punya sampul sama sekali, jadikan file pertama sebagai sampul
                if (!$imagePath) {
                    $imagePath = $files[0]->store('gallery', 'public');
                }
                
                foreach ($files as $file) {
                    $additionalImagePaths[] = $file->store('gallery', 'public');
                }
            }
            
            $youtubeId = null; // Kosongkan youtube_id jika tipe diubah ke foto
        } elseif ($request->input('type') === 'video') {
            $url = $request->input('youtube_url');
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
            $newYoutubeId = $match[1] ?? null;

            if ($newYoutubeId) {
                $youtubeId = $newYoutubeId;
                // Hapus file fisik gambar lokal jika sebelumnya adalah foto
                self::deletePhysicalFile($gallery->image);
                $imagePath = 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg';
            }
        }

        $catModel = \App\Models\Category::find($categoryId);
        
        $gallery->title = $request->input('title');
        $gallery->category_id = $categoryId;
        $gallery->category = $catModel->name ?? 'Kegiatan';
        $gallery->caption = $request->input('caption');
        $gallery->type = $request->input('type');
        $gallery->youtube_id = $youtubeId;
        $gallery->image = $imagePath;
        $gallery->show_on_homepage = $request->has('show_on_homepage');
        $gallery->is_active = $request->boolean('is_active');

        // Update tanggal kegiatan jika disediakan
        if ($request->filled('created_at')) {
            try {
                $gallery->created_at = \Carbon\Carbon::parse($request->input('created_at'));
            } catch (\Throwable $e) {}
        }

        $gallery->save();

        // Simpan foto tambahan ke album jika ada
        if (!empty($additionalImagePaths)) {
            foreach ($additionalImagePaths as $path) {
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path
                ]);
            }
        }

        $label = $gallery->type === 'video' ? 'Video kegiatan' : 'Foto galeri kegiatan';
        return back()->with('status', $label . ' berhasil diperbarui.');
    }

    /**
     * Hapus foto satuan dari album galeri sekaligus hapus berkas fisik dari server.
     */
    public function destroyImage($galleryId, $imageId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $image = GalleryImage::where('gallery_id', $galleryId)->findOrFail($imageId);

        // Hapus berkas fisik dari penyimpanan server
        self::deletePhysicalFile($image->image_path);

        // Jika foto yang dihapus kebetulan adalah sampul utama, ganti ke foto album berikutnya atau null
        if ($gallery->image === $image->image_path) {
            $image->delete();
            $nextImage = $gallery->images()->first();
            if ($nextImage) {
                $gallery->update(['image' => $nextImage->image_path]);
            } else {
                $gallery->update(['image' => null]);
            }
        } else {
            $image->delete();
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Berkas foto fisik dan data album berhasil dihapus permanen.'
            ]);
        }

        return back()->with('status', 'Foto berhasil dihapus dari album dan penyimpanan.');
    }

    /**
     * Hapus keseluruhan album/foto/video dari galeri sekaligus hapus semua berkas fisik dari server.
     */
    public function destroy($id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);
        $type = $gallery->type ?? 'foto';
        $title = $gallery->title;
        
        // 1. Hapus semua berkas fisik foto tambahan di album
        foreach ($gallery->images as $img) {
            self::deletePhysicalFile($img->image_path);
            $img->delete();
        }

        // 2. Hapus berkas fisik foto sampul
        self::deletePhysicalFile($gallery->image);
        
        // 3. Hapus record galeri dari database
        $gallery->delete();

        $label = $type === 'video' ? 'Video kegiatan' : 'Album foto kegiatan';

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $label . ' "' . $title . '" beserta seluruh berkas foto fisiknya berhasil dihapus permanen.'
            ]);
        }

        return back()->with('status', $label . ' "' . $title . '" berhasil dihapus permanen.');
    }

    /**
     * Toggle status tampil di beranda
     */
    public function toggleHomepage($id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->show_on_homepage = !$gallery->show_on_homepage;
        $gallery->save();

        return back()->with('status', 'Status tampil di beranda berhasil diubah.');
    }
}
