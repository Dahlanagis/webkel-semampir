<?php

namespace Database\Seeders;

use App\Models\LetterRequest;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LetterRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'Surat Keterangan Usaha (SKU)',
                'slug' => 'sku',
                'code' => 'SKU',
                'description' => 'Surat keterangan untuk keperluan legalitas usaha mikro/kecil warga Kelurahan Patokan.',
                'required_documents' => ['Foto KTP Warga', 'Kartu Keluarga (KK)', 'Foto Tempat Usaha', 'Surat Pengantar RT/RW'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'slug' => 'sktm',
                'code' => 'SKTM',
                'description' => 'Surat keterangan untuk permohonan beasiswa, bantuan medis, atau fasilitas pemerintah.',
                'required_documents' => ['Foto KTP Warga', 'Kartu Keluarga (KK)', 'Surat Pernyataan Tidak Mampu', 'Surat Pengantar RT/RW'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Domisili',
                'slug' => 'domisili',
                'code' => 'SKD',
                'description' => 'Surat keterangan verifikasi alamat dan domisili tinggal warga di Kelurahan Patokan.',
                'required_documents' => ['Foto KTP Warga', 'Kartu Keluarga (KK)', 'Surat Pengantar RT/RW'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Kelahiran',
                'slug' => 'kelahiran',
                'code' => 'SKK',
                'description' => 'Surat pengantar pendataan kelahiran anak untuk pembuatan Akta Kelahiran di Disdukcapil.',
                'required_documents' => ['KTP Orang Tua', 'Kartu Keluarga (KK)', 'Surat Keterangan Bidan/RS', 'KTP Saksi'],
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Belum Menikah',
                'slug' => 'belum-menikah',
                'code' => 'SKBM',
                'description' => 'Surat keterangan status lajang/belum menikah untuk keperluan pekerjaan atau pernikahan.',
                'required_documents' => ['Foto KTP Warga', 'Kartu Keluarga (KK)', 'Surat Pernyataan Belum Menikah', 'Surat Pengantar RT/RW'],
                'is_active' => true,
            ],
        ];

        $createdServiceTypes = [];
        foreach ($serviceTypes as $st) {
            $createdServiceTypes[$st['code']] = ServiceType::create($st);
        }

        $requests = [
            [
                'tracking_code' => 'SRT-20260901-001',
                'nik' => '3513011205850001',
                'resident_name' => 'Budi Santoso',
                'phone_number' => '081234567890',
                'address' => 'Jl. Panglima Sudirman No. 12, RT 02 / RW 01, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKU']->id,
                'status' => 'completed',
                'attachment_path' => null,
                'admin_notes' => 'Surat telah diterbitkan dan dapat diambil di meja layanan publik Kelurahan Patokan.',
                'completed_at' => Carbon::now()->subDays(3),
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'tracking_code' => 'SRT-20260902-002',
                'nik' => '3513015508920003',
                'resident_name' => 'Siti Aminah',
                'phone_number' => '082198765432',
                'address' => 'Jl. Basuki Rahmat Gg. 3 No. 5, RT 01 / RW 03, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKTM']->id,
                'status' => 'processing',
                'attachment_path' => null,
                'admin_notes' => 'Sedang dalam verifikasi verifikator lapangan Kasi Pelayanan Masyarakat.',
                'completed_at' => null,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'tracking_code' => 'SRT-20260903-003',
                'nik' => '3513011003780002',
                'resident_name' => 'Ahmad Dahlan',
                'phone_number' => '085712344321',
                'address' => 'Jl. Ahmad Yani No. 88, RT 03 / RW 02, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKD']->id,
                'status' => 'pending',
                'attachment_path' => null,
                'admin_notes' => null,
                'completed_at' => null,
                'created_at' => Carbon::now()->subDay(),
            ],
            [
                'tracking_code' => 'SRT-20260903-004',
                'nik' => '3513014411950004',
                'resident_name' => 'Rina Wijaya',
                'phone_number' => '083899001122',
                'address' => 'Gg. Keramat No. 14, RT 04 / RW 01, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKTM']->id,
                'status' => 'pending',
                'attachment_path' => null,
                'admin_notes' => null,
                'completed_at' => null,
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'tracking_code' => 'SRT-20260904-005',
                'nik' => '3513012006880005',
                'resident_name' => 'Joko Widodo',
                'phone_number' => '081377889900',
                'address' => 'Jl. Diponegoro No. 45, RT 02 / RW 04, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKU']->id,
                'status' => 'rejected',
                'attachment_path' => null,
                'admin_notes' => 'Dokumen Pengantar RT/RW belum ditandatangani Ketua RW.',
                'completed_at' => null,
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'tracking_code' => 'SRT-20260904-006',
                'nik' => '3513016201990006',
                'resident_name' => 'Dewi Lestari',
                'phone_number' => '085211223344',
                'address' => 'Jl. Vetran Gg. Anggrek No. 2, RT 01 / RW 02, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKK']->id,
                'status' => 'processing',
                'attachment_path' => null,
                'admin_notes' => 'Validasi data saksi dan surat bidan pendamping.',
                'completed_at' => null,
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'tracking_code' => 'SRT-20260904-007',
                'nik' => '3513010507900007',
                'resident_name' => 'Hendra Gunawan',
                'phone_number' => '087855667788',
                'address' => 'Jl. Hayam Wuruk No. 19, RT 05 / RW 03, Kel. Patokan',
                'service_type_id' => $createdServiceTypes['SKBM']->id,
                'status' => 'pending',
                'attachment_path' => null,
                'admin_notes' => null,
                'completed_at' => null,
                'created_at' => Carbon::now()->subMinutes(45),
            ],
        ];

        foreach ($requests as $req) {
            LetterRequest::create($req);
        }
    }
}
