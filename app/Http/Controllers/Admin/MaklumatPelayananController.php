<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MaklumatPelayananController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = storage_path('app/village_profile.json');
    }

    public function index()
    {
        $profile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        return view('admin.beranda.maklumat', compact('profile'));
    }

    /**
     * Perbarui data Maklumat Pelayanan publik termasuk upload berkas/foto piagam resmi.
     */
    public function update(Request $request)
    {
        $request->validate([
            'maklumat_text' => 'nullable|string',
            'maklumat_nomor_sk' => 'nullable|string|max:150',
            'maklumat_caption' => 'nullable|string|max:255',
            'maklumat_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:3072',
        ], [
            'maklumat_file.mimes' => 'Format berkas tidak valid! Hanya mendukung file JPG, PNG, WEBP, atau PDF.',
            'maklumat_file.max' => 'Ukuran berkas melebihi batas! Maksimal ukuran berkas adalah 3 MB.',
        ]);

        $existingData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        
        $existingData['maklumat_text'] = $request->input('maklumat_text', '');
        $existingData['maklumat_nomor_sk'] = $request->input('maklumat_nomor_sk', '188.45/04/426.411.01/2026');
        $existingData['maklumat_caption'] = $request->input('maklumat_caption', 'Dokumen Piagam Resmi Penetapan Standar Maklumat Pelayanan Publik Kelurahan Semampir');

        // Opsi hapus berkas
        if ($request->boolean('delete_maklumat_file')) {
            if (!empty($existingData['maklumat_file']) && Storage::disk('public')->exists($existingData['maklumat_file'])) {
                Storage::disk('public')->delete($existingData['maklumat_file']);
            }
            $existingData['maklumat_file'] = null;
            $existingData['maklumat_file_type'] = null;
            $existingData['maklumat_file_name'] = null;
        }

        // Upload berkas baru jika ada
        if ($request->hasFile('maklumat_file')) {
            // Hapus berkas lama jika sebelumnya sudah ada
            if (!empty($existingData['maklumat_file']) && Storage::disk('public')->exists($existingData['maklumat_file'])) {
                Storage::disk('public')->delete($existingData['maklumat_file']);
            }

            $file = $request->file('maklumat_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $storedPath = $file->store('maklumat', 'public');

            $existingData['maklumat_file'] = $storedPath;
            $existingData['maklumat_file_type'] = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : 'pdf';
            $existingData['maklumat_file_name'] = $originalName;
            $existingData['maklumat_file_size'] = round($file->getSize() / 1024, 1) . ' KB';
            $existingData['maklumat_updated_at'] = now()->format('d M Y, H:i');
        }

        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', 'Maklumat Pelayanan dan berkas piagam berhasil diperbarui.');
    }
}

