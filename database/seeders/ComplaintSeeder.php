<?php

namespace Database\Seeders;

use App\Models\Complaint;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $complaints = [
            ['resident_name' => 'Ahmad Fauzi', 'nik' => '3513010101900001', 'phone_number' => '081234567001', 'email' => 'ahmad.fauzi@mail.com', 'subject' => 'Jalan berlubang di RT 03', 'category' => 'infrastruktur', 'description' => 'Jalan di depan musholla RT 03 RW 02 mengalami kerusakan parah dan berlubang besar sehingga membahayakan pengendara motor terutama pada malam hari. Mohon segera ditangani.', 'status' => 'in_review', 'admin_notes' => 'Sudah dilaporkan ke Dinas PU. Akan dijadwalkan perbaikan.'],
            ['resident_name' => 'Siti Nurhaliza', 'nik' => '3513010202880002', 'phone_number' => '081234567002', 'subject' => 'Lampu penerangan jalan mati', 'category' => 'infrastruktur', 'description' => 'Lampu jalan di sepanjang Gang Melati RW 04 sudah mati sejak 2 minggu lalu. Membuat warga merasa tidak aman pada malam hari. Mohon segera diperbaiki.', 'status' => 'resolved', 'admin_notes' => 'Sudah diperbaiki per tanggal 5 September 2026.', 'resolved_at' => now()->subDays(3)],
            ['resident_name' => 'Budi Santoso', 'nik' => '3513010303920003', 'phone_number' => '081234567003', 'subject' => 'Pelayanan KTP lambat', 'category' => 'pelayanan', 'description' => 'Sudah mengurus perpanjangan KTP sejak bulan lalu namun hingga saat ini belum ada kabar. Proses terlalu lama dan tidak ada kejelasan timeline penyelesaian.', 'status' => 'pending'],
            ['resident_name' => 'Dewi Kartini', 'nik' => '3513010404950004', 'phone_number' => '081234567004', 'subject' => 'Sampah menumpuk di TPS', 'category' => 'kebersihan', 'description' => 'Tempat Pembuangan Sementara di belakang pasar desa sudah menumpuk tinggi dan belum diangkut selama seminggu. Menimbulkan bau tidak sedap dan lalat banyak.', 'status' => 'in_review'],
            ['resident_name' => 'Hasan Basri', 'nik' => '3513010505870005', 'phone_number' => '081234567005', 'subject' => 'Keributan malam hari', 'category' => 'keamanan', 'description' => 'Setiap malam Sabtu selalu ada keributan di warung kopi pojok RT 05 hingga larut malam. Mengganggu ketenangan warga sekitar. Mohon ada tindakan dari aparat.', 'status' => 'resolved', 'admin_notes' => 'Telah dikoordinasikan dengan Babinsa dan RT setempat.', 'resolved_at' => now()->subDays(5)],
            ['resident_name' => 'Nur Aini', 'nik' => '3513010606910006', 'phone_number' => '081234567006', 'subject' => 'Drainase tersumbat', 'category' => 'infrastruktur', 'description' => 'Saluran air di depan rumah saya RT 02 RW 01 tersumbat sampah dan setiap hujan deras selalu banjir masuk ke rumah warga. Mohon segera dibersihkan.', 'status' => 'pending'],
            ['resident_name' => 'Rudi Hermawan', 'nik' => '3513010707850007', 'subject' => 'Usulan penambahan posyandu', 'category' => 'lainnya', 'description' => 'Mengusulkan penambahan jadwal posyandu di RW 06 karena jumlah balita cukup banyak dan jarak ke posyandu terdekat cukup jauh. Minimal 2x sebulan.', 'status' => 'pending'],
            ['resident_name' => 'Eko Prasetyo', 'nik' => '3513010808930008', 'phone_number' => '081234567008', 'subject' => 'Pohon tumbang menghalangi jalan', 'category' => 'keamanan', 'description' => 'Ada pohon besar tumbang di jalan utama desa setelah hujan deras tadi malam. Menghalangi akses kendaraan dan berbahaya. Perlu segera ditangani.', 'status' => 'rejected', 'admin_notes' => 'Sudah ditangani oleh BPBD. Pohon sudah dipindahkan.'],
            ['resident_name' => 'Maya Sari', 'nik' => '3513010909960009', 'phone_number' => '081234567009', 'email' => 'maya.sari@mail.com', 'subject' => 'Kerusakan saluran air bersih', 'category' => 'infrastruktur', 'description' => 'Pipa saluran air PDAM di area RT 04 RW 03 bocor sejak 3 hari lalu. Air terbuang percuma dan tekanan air di rumah warga menurun drastis.', 'status' => 'in_review'],
            ['resident_name' => 'Agus Wahyudi', 'nik' => '3513011010880010', 'phone_number' => '081234567010', 'subject' => 'Laporan anjing liar', 'category' => 'keamanan', 'description' => 'Ada beberapa anjing liar yang berkeliaran di area RW 07 dan mengganggu warga terutama anak-anak. Mohon ada penertiban dari pihak berwenang.', 'status' => 'pending'],
        ];

        foreach ($complaints as $data) {
            Complaint::create($data);
        }
    }
}
