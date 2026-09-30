<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActivityLog::truncate();

        $admin = User::where('role', 'admin')->first();
        $adminName = $admin?->name ?? 'Administrator Kelurahan';
        $adminId = $admin?->id ?? 1;

        $items = [
            [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'LOGIN',
                'description' => 'Administrator berhasil masuk ke dalam sistem SIMPEL Kelurahan Semampir.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'created_at' => now()->subHours(5),
            ],
            [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'UPDATE',
                'description' => 'Mengubah data profil kelurahan dan data angka statistik beranda (Penduduk, KK, RT/RW).',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'created_at' => now()->subHours(4),
            ],
            [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'UPDATE',
                'description' => 'Mengunggah dokumen PDF Standar Pelayanan Publik dan SOP Surat Keterangan Usaha (SKU).',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'created_at' => now()->subHours(3),
            ],
            [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'CREATE',
                'description' => 'Menerbitkan berita baru: "Pemerintah Kelurahan Semampir Sambut Musim Hujan dan Jaga Kebersihan Lingkungan".',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'UPDATE',
                'description' => 'Mengunggah dokumen Transparansi Anggaran Perencanaan & Laporan APBDes 2026.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'created_at' => now()->subHour(),
            ],
            [
                'user_id' => $adminId,
                'user_name' => $adminName,
                'action' => 'CREATE',
                'description' => 'Menambahkan 18 foto kegiatan gotong royong dan posyandu ke Galeri Dokumentasi Kelurahan.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'created_at' => now()->subMinutes(30),
            ],
        ];

        foreach ($items as $item) {
            ActivityLog::create($item);
        }
    }
}
