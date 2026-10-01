<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = storage_path('app/system_settings.json');
    }

    /**
     * Ambil data pengaturan sistem.
     */
    public static function getSettings(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $val = \App\Models\Setting::get('system_settings');
                if (!empty($val)) {
                    $decoded = json_decode($val, true);
                    if (is_array($decoded)) {
                        return $decoded;
                    }
                }
            }
        } catch (\Throwable $e) {}

        $path = storage_path('app/system_settings.json');
        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            if (is_array($data)) {
                return $data;
            }
        }

        return [
            'app_name' => 'SIMPEL KELURAHAN',
            'app_subtitle' => 'Sistem Informasi Manajemen Pelayanan Kelurahan Semampir',
            'maintenance_mode' => false,
            'max_upload_mb' => 3,
            'items_per_page' => 10,
            'app_logo' => null,
        ];
    }

    /**
     * Tampilkan halaman pengaturan sistem.
     */
    public function index()
    {
        $settings = self::getSettings();
        return view('admin.pengaturan.index', compact('settings'));
    }

    /**
     * Perbarui pengaturan sistem.
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_subtitle' => 'nullable|string|max:500',
            'maintenance_mode' => 'boolean',
            'max_upload_mb' => 'required|integer|min:1|max:20',
            'app_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ], [
            'app_name.required' => 'Nama aplikasi wajib diisi.',
            'max_upload_mb.required' => 'Batas upload file wajib diisi.',
            'app_logo.image' => 'File ditolak! Logo aplikasi harus berupa file gambar.',
            'app_logo.mimes' => 'File ditolak! Format logo aplikasi harus JPG, PNG, WEBP, atau SVG.',
            'app_logo.max' => 'File ditolak! Ukuran file logo aplikasi maksimal 2 MB.',
        ]);

        $settings = self::getSettings();

        $settings['app_name'] = $request->input('app_name');
        $settings['app_subtitle'] = $request->input('app_subtitle');
        $settings['maintenance_mode'] = $request->boolean('maintenance_mode');
        $settings['max_upload_mb'] = (int) $request->input('max_upload_mb');

        if ($request->hasFile('app_logo')) {
            if (!empty($settings['app_logo']) && Storage::disk('public')->exists($settings['app_logo'])) {
                Storage::disk('public')->delete($settings['app_logo']);
            }
            $settings['app_logo'] = $request->file('app_logo')->store('settings', 'public');
        }

        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                \App\Models\Setting::set('system_settings', $json);
            }
        } catch (\Throwable $e) {}

        $savePaths = array_unique([
            $this->configPath,
            storage_path('app/system_settings.json'),
            base_path('storage/app/system_settings.json'),
        ]);
        foreach ($savePaths as $savePath) {
            try {
                $dir = dirname($savePath);
                if (!File::isDirectory($dir)) {
                    @File::makeDirectory($dir, 0755, true);
                }
                @File::put($savePath, $json);
            } catch (\Throwable $e) {}
        }

        ActivityLog::record('UPDATE', 'Memperbarui konfigurasi & pengaturan sistem aplikasi.');

        return back()->with('status', 'Pengaturan sistem berhasil diperbarui.');
    }
}
