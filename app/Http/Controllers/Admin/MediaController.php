<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MediaController extends Controller
{
    /**
     * Display media library files.
     */
    public function index(Request $request)
    {
        $allFiles = Storage::disk('public')->allFiles();
        
        $filesData = collect($allFiles)->map(function ($filePath) {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            $isPdf = $extension === 'pdf';
            
            return [
                'path' => $filePath,
                'name' => basename($filePath),
                'url' => asset('storage/' . $filePath),
                'size' => Storage::disk('public')->size($filePath),
                'last_modified' => Storage::disk('public')->lastModified($filePath),
                'extension' => $extension,
                'is_image' => $isImage,
                'is_pdf' => $isPdf,
            ];
        })->sortByDesc('last_modified')->values();

        // Search filter
        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $filesData = $filesData->filter(function ($item) use ($q) {
                return str_contains(strtolower($item['name']), $q) || str_contains(strtolower($item['path']), $q);
            })->values();
        }

        // Type filter
        if ($request->filled('type')) {
            $type = $request->input('type');
            if ($type === 'image') {
                $filesData = $filesData->where('is_image', true)->values();
            } elseif ($type === 'pdf') {
                $filesData = $filesData->where('is_pdf', true)->values();
            }
        }

        // Pagination
        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 16;
        $items = $filesData->forPage($page, $perPage)->values();
        
        $paginatedFiles = new LengthAwarePaginator(
            $items,
            $filesData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.media.index', [
            'files' => $paginatedFiles,
            'totalFiles' => $filesData->count(),
        ]);
    }

    /**
     * Upload a new media file.
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file' => 'required|file|max:102400', // Max 100MB
            'folder' => 'nullable|string|max:50',
        ], [
            'file.required' => 'Pilih berkas media yang ingin diunggah.',
            'file.file' => 'File ditolak! Berkas yang diunggah tidak valid.',
            'file.max' => 'File ditolak! Ukuran berkas maksimal 100 MB.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax() || str_contains($request->header('Accept', ''), 'application/json')) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $folder = $request->input('folder', 'uploads');
        $folder = preg_replace('/[^a-zA-Z0-9_\-]/', '', $folder) ?: 'uploads';

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = time() . '_' . \Str::slug($originalName) . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs($folder, $filename, 'public');

        ActivityLog::record('CREATE', "Mengunggah berkas media baru: {$path}");

        if ($request->expectsJson() || $request->ajax() || str_contains($request->header('Accept', ''), 'application/json') || $request->header('origin')) {
            $url = asset('storage/' . $path);
            return response()->json([
                'location' => $url,
                'url' => $url,
                'filename' => $filename,
            ]);
        }

        return back()->with('status', "Berkas media '{$filename}' berhasil diunggah.");
    }

    /**
     * Delete a media file.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            ActivityLog::record('DELETE', "Menghapus berkas media: {$path}");
            return back()->with('status', 'Berkas media berhasil dihapus.');
        }

        return back()->with('warning', 'Berkas media tidak ditemukan.');
    }
}
