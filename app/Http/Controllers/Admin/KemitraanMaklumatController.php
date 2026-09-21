<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class KemitraanMaklumatController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = storage_path('app/village_profile.json');
    }

    public function maklumat()
    {
        $profile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        return view('admin.beranda.maklumat', compact('profile'));
    }

    public function kemitraan()
    {
        $profile = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        return view('admin.beranda.kemitraan', compact('profile'));
    }

    /**
     * Perbarui data Kemitraan atau Maklumat Pelayanan.
     */
    public function update(Request $request)
    {
        $existingData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $section = $request->input('section');
        $statusMsg = 'Data berhasil diperbarui.';

        if ($section === 'maklumat') {
            $existingData['maklumat_text'] = $request->input('maklumat_text', '');
            $statusMsg = 'Maklumat Pelayanan berhasil diperbarui.';
        } elseif ($section === 'kemitraan') {
            $partnerNames = $request->input('partner_name', []);
            $partnerUrls = $request->input('partner_url', []);
            $partnerDescs = $request->input('partner_desc', []);
            $existingLogos = $request->input('existing_logo', []);

            $partners = [];
            foreach ($partnerNames as $i => $name) {
                if (!empty($name)) {
                    $logoPath = $existingLogos[$i] ?? '';
                    
                    // Cek jika ada upload logo baru
                    if ($request->hasFile("partner_logo.{$i}")) {
                        // Hapus logo lama jika ada
                        if (!empty($logoPath) && Storage::disk('public')->exists($logoPath)) {
                            Storage::disk('public')->delete($logoPath);
                        }
                        $logoPath = $request->file("partner_logo.{$i}")->store('kemitraan', 'public');
                    }

                    $partners[] = [
                        'name' => $name,
                        'url' => $partnerUrls[$i] ?? '#',
                        'desc' => $partnerDescs[$i] ?? '',
                        'logo' => $logoPath
                    ];
                }
            }

            // Hapus gambar dari storage jika dihapus di form
            $currentPartners = $existingData['kemitraan'] ?? [];
            $newLogoPaths = array_column($partners, 'logo');
            foreach ($currentPartners as $oldPartner) {
                if (!empty($oldPartner['logo']) && !in_array($oldPartner['logo'], $newLogoPaths)) {
                    if (Storage::disk('public')->exists($oldPartner['logo'])) {
                        Storage::disk('public')->delete($oldPartner['logo']);
                    }
                }
            }

            $existingData['kemitraan'] = $partners;
            $statusMsg = 'Daftar Kemitraan berhasil diperbarui.';
        }

        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', $statusMsg);
    }

    public function storeMitra(Request $request)
    {
        $existingData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $kemitraan = $existingData['kemitraan'] ?? [];

        $logoPath = '';
        if ($request->hasFile('partner_logo')) {
            $logoPath = $request->file('partner_logo')->store('kemitraan', 'public');
        }

        $kemitraan[] = [
            'name' => $request->input('partner_name'),
            'url' => $request->input('partner_url', '#'),
            'desc' => $request->input('partner_desc', ''),
            'logo' => $logoPath
        ];

        $existingData['kemitraan'] = $kemitraan;
        File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('success', 'Mitra baru berhasil ditambahkan.');
    }

    public function updateMitra(Request $request, $index)
    {
        $existingData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $kemitraan = $existingData['kemitraan'] ?? [];

        if (isset($kemitraan[$index])) {
            $logoPath = $kemitraan[$index]['logo'] ?? '';
            
            if ($request->hasFile('partner_logo')) {
                if (!empty($logoPath) && Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('partner_logo')->store('kemitraan', 'public');
            }

            $kemitraan[$index] = [
                'name' => $request->input('partner_name'),
                'url' => $request->input('partner_url', '#'),
                'desc' => $request->input('partner_desc', ''),
                'logo' => $logoPath
            ];

            $existingData['kemitraan'] = $kemitraan;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Data mitra berhasil diperbarui.');
        }

        return back()->withErrors(['Mitra tidak ditemukan.']);
    }

    public function destroyMitra($index)
    {
        $existingData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $kemitraan = $existingData['kemitraan'] ?? [];

        if (isset($kemitraan[$index])) {
            $logoPath = $kemitraan[$index]['logo'] ?? '';
            if (!empty($logoPath) && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            
            array_splice($kemitraan, $index, 1);
            $existingData['kemitraan'] = $kemitraan;
            File::put($this->configPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Mitra berhasil dihapus.');
        }

        return back()->withErrors(['Mitra tidak ditemukan.']);
    }
}
