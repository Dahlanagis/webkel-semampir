<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelatedLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
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
     * Helper untuk menyimpan file JSON konfigurasi ke beberapa path writable.
     */
    protected function saveProfileData(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $paths = [
            $this->configPath,
            storage_path('app/village_profile.json'),
            base_path('storage/app/village_profile.json'),
        ];

        foreach (array_unique($paths) as $path) {
            try {
                $dir = dirname($path);
                if (!File::isDirectory($dir)) {
                    @File::makeDirectory($dir, 0755, true);
                }
                @File::put($path, $json);
            } catch (\Throwable $e) {
                // Ignore write failures on read-only locations
            }
        }
    }

    /**
     * Sinkronisasi data kemitraan ke tabel database related_links.
     */
    protected function syncRelatedLinksToDb(array $partners): void
    {
        try {
            if (Schema::hasTable('related_links')) {
                RelatedLink::truncate();
                foreach ($partners as $i => $partner) {
                    RelatedLink::create([
                        'name' => $partner['name'] ?? '',
                        'url' => $partner['url'] ?? '#',
                        'desc' => $partner['desc'] ?? '',
                        'logo' => $partner['logo'] ?? '',
                        'order' => $i + 1,
                        'is_active' => true,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore if DB fails
        }
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
            $this->syncRelatedLinksToDb($partners);
            $statusMsg = 'Daftar Link Terkait berhasil diperbarui.';
        }

        $this->saveProfileData($existingData);

        return back()->with('status', $statusMsg)->with('success', $statusMsg);
    }

    public function storeMitra(Request $request)
    {
        $existingData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        $kemitraan = $existingData['kemitraan'] ?? [];

        $logoPath = '';
        if ($request->hasFile('partner_logo')) {
            $logoPath = $request->file('partner_logo')->store('kemitraan', 'public');
        }

        $newPartner = [
            'name' => $request->input('partner_name'),
            'url' => $request->input('partner_url', '#'),
            'desc' => $request->input('partner_desc', ''),
            'logo' => $logoPath
        ];

        $kemitraan[] = $newPartner;
        $existingData['kemitraan'] = $kemitraan;

        // Simpan ke database
        try {
            if (Schema::hasTable('related_links')) {
                RelatedLink::create([
                    'name' => $newPartner['name'],
                    'url' => $newPartner['url'],
                    'desc' => $newPartner['desc'],
                    'logo' => $newPartner['logo'],
                    'order' => count($kemitraan),
                    'is_active' => true,
                ]);
            }
        } catch (\Throwable $e) {}

        $this->saveProfileData($existingData);

        return back()->with('status', 'Link terkait baru berhasil ditambahkan.')->with('success', 'Link terkait baru berhasil ditambahkan.');
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

            $updatedPartner = [
                'name' => $request->input('partner_name'),
                'url' => $request->input('partner_url', '#'),
                'desc' => $request->input('partner_desc', ''),
                'logo' => $logoPath
            ];

            $kemitraan[$index] = $updatedPartner;
            $existingData['kemitraan'] = $kemitraan;

            // Sinkronkan ke database
            $this->syncRelatedLinksToDb($kemitraan);
            $this->saveProfileData($existingData);

            return back()->with('status', 'Data link terkait berhasil diperbarui.')->with('success', 'Data link terkait berhasil diperbarui.');
        }

        return back()->withErrors(['Link terkait tidak ditemukan.']);
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

            // Sinkronkan ke database
            $this->syncRelatedLinksToDb($kemitraan);
            $this->saveProfileData($existingData);

            return back()->with('status', 'Link terkait berhasil dihapus.')->with('success', 'Link terkait berhasil dihapus.');
        }

        return back()->withErrors(['Link terkait tidak ditemukan.']);
    }
}
