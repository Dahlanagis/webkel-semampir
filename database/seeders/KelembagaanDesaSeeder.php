<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LembagaDesa;
use App\Models\BumdesDetail;
use Illuminate\Support\Str;

class KelembagaanDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lembagas = [
            [
                'nama_lembaga' => 'Rukun Tetangga dan Rukun Warga',
                'singkatan' => 'RT / RW',
                'slug' => 'rt-rw-kelurahan-semampir',
                'jenis_lembaga' => 'LKD',
                'nomor_sk_pendirian' => '140/01/SK/SMP/2024',
                'tanggal_sk' => '2024-01-10',
                'dasar_hukum' => 'Permendagri No. 18 Tahun 2018 tentang Lembaga Kemasyarakatan Desa dan Lembaga Adat Desa',
                'nama_ketua' => 'Koordinator Forum RT/RW Kelurahan Semampir',
                'kontak' => '081234567801',
                'alamat_kantor' => 'Kantor Kelurahan Semampir, Ruang Pelayanan Warga',
                'deskripsi_profil' => 'Lembaga kemasyarakatan yang dibentuk melalui musyawarah masyarakat setempat dalam rangka pelayanan pemerintahan dan kemasyarakatan yang diakui dan dibina oleh Pemerintah Kelurahan.',
                'status_aktif' => true,
            ],
            [
                'nama_lembaga' => 'Tim Penggerak Pemberdayaan dan Kesejahteraan Keluarga',
                'singkatan' => 'TP-PKK',
                'slug' => 'tp-pkk-kelurahan-semampir',
                'jenis_lembaga' => 'LKD',
                'nomor_sk_pendirian' => '140/02/SK/SMP/2024',
                'tanggal_sk' => '2024-01-15',
                'dasar_hukum' => 'Permendagri No. 36 Tahun 2020 tentang Peraturan Pelaksanaan Perpres No. 99 Tahun 2017 tentang Gerakan PKK',
                'nama_ketua' => 'Ny. Hj. Rahmawati, S.Pd.',
                'kontak' => '081234567802',
                'alamat_kantor' => 'Gedung Serbaguna PKK Kelurahan Semampir',
                'deskripsi_profil' => 'Gerakan nasional dalam pembangunan masyarakat yang tumbuh dari bawah dengan pengelolaan dari, oleh dan untuk masyarakat guna mewujudkan keluarga beriman, bertaqwa, dan berakhlak mulia.',
                'status_aktif' => true,
            ],
            [
                'nama_lembaga' => 'Karang Taruna Tunas Harapan',
                'singkatan' => 'Karang Taruna',
                'slug' => 'karang-taruna-tunas-harapan',
                'jenis_lembaga' => 'LKD',
                'nomor_sk_pendirian' => '140/03/SK/SMP/2024',
                'tanggal_sk' => '2024-02-01',
                'dasar_hukum' => 'Permensos No. 25 Tahun 2019 tentang Karang Taruna',
                'nama_ketua' => 'Dimas Aditya Pratama',
                'kontak' => '081234567803',
                'alamat_kantor' => 'Sekretariat Pemuda, Jl. Pemuda No. 04 Semampir',
                'deskripsi_profil' => 'Wadah pengembangan generasi muda yang tumbuh atas dasar kesadaran dan tanggung jawab sosial dari, oleh, dan untuk generasi muda yang berorientasi pada tercapainya kesejahteraan sosial.',
                'status_aktif' => true,
            ],
            [
                'nama_lembaga' => 'Pos Pelayanan Terpadu Mandiri',
                'singkatan' => 'Posyandu',
                'slug' => 'posyandu-kelurahan-semampir',
                'jenis_lembaga' => 'LKD',
                'nomor_sk_pendirian' => '140/04/SK/SMP/2024',
                'tanggal_sk' => '2024-02-10',
                'dasar_hukum' => 'Permendagri No. 19 Tahun 2011 tentang Pengintegrasian Layanan Sosial Dasar di Posyandu',
                'nama_ketua' => 'Ibu Siti Munawaroh, A.Md.Keb.',
                'kontak' => '081234567804',
                'alamat_kantor' => 'Poskesdes / Balai Posyandu Semampir',
                'deskripsi_profil' => 'Wadah pemeliharaan kesehatan berbasis masyarakat yang melayani pemantauan tumbuh kembang balita, imunisasi, gizi, kesehatan ibu dan anak, serta posyandu lansia.',
                'status_aktif' => true,
            ],
            [
                'nama_lembaga' => 'Lembaga Pemberdayaan Masyarakat Kelurahan',
                'singkatan' => 'LPMK / LPMD',
                'slug' => 'lpmk-kelurahan-semampir',
                'jenis_lembaga' => 'LKD',
                'nomor_sk_pendirian' => '140/05/SK/SMP/2024',
                'tanggal_sk' => '2024-02-15',
                'dasar_hukum' => 'Perda tentang Pedoman Pembentukan Lembaga Pemberdayaan Masyarakat Kelurahan',
                'nama_ketua' => 'H. Suwandi Kartasasmita',
                'kontak' => '081234567805',
                'alamat_kantor' => 'Ruang Musrenbang Kantor Kelurahan Semampir',
                'deskripsi_profil' => 'Mitra kelurahan dalam menampung dan menyalurkan aspirasi masyarakat dalam musyawarah perencanaan pembangunan (Musrenbang) serta menggerakkan partisipasi gotong royong.',
                'status_aktif' => true,
            ],
            [
                'nama_lembaga' => 'BUMDes Semampir Makmur Sejahtera',
                'singkatan' => 'BUMDes Semampir',
                'slug' => 'bumdes-semampir-makmur-sejahtera',
                'jenis_lembaga' => 'BUMDes',
                'nomor_sk_pendirian' => '140/06/SK/SMP/2023',
                'tanggal_sk' => '2023-08-17',
                'dasar_hukum' => 'PP No. 11 Tahun 2021 tentang Badan Usaha Milik Desa / Kelurahan',
                'nama_ketua' => 'Bambang Sudarmono, S.E.',
                'kontak' => '081234567890',
                'alamat_kantor' => 'Gedung Sentra Usaha BUMDes, Jl. Raya Semampir No. 12',
                'deskripsi_profil' => 'Badan hukum yang didirikan oleh kelurahan guna mengelola usaha, memanfaatkan aset, mengembangkan investasi dan produktivitas, menyediakan jasa pelayanan, dan menyediakan jenis usaha lainnya untuk kesejahteraan warga.',
                'status_aktif' => true,
                'bumdes_detail' => [
                    'nomor_badan_hukum_kemenkumham' => 'AHU-01234.AH.01.33.TAHUN 2023',
                    'tahun_pendirian' => 2023,
                    'npwp_bumdes' => '82.345.678.9-605.000',
                    'kategori_status' => 'Maju',
                    'permodalan_awal' => 150000000,
                    'total_aset' => 485000000,
                    'omzet_terakhir' => 210000000,
                    'daftar_unit_usaha' => 'Pengelolaan Sampah Terpadu & Daur Ulang, Sentra Pemasaran UMKM Kuliner, Jasa Pengelolaan Air Bersih, Toko Sarana Produksi',
                    'nama_penasihat' => 'Lurah Semampir (Ex-Officio)',
                    'nama_pelaksana_operasional' => 'Bambang Sudarmono, S.E. (Direktur Utama)',
                    'nama_pengawas' => 'Drs. H. Mulyono & Tim Pengawas BPD/Kelurahan',
                ],
            ],
        ];

        foreach ($lembagas as $item) {
            $bumdesData = $item['bumdes_detail'] ?? null;
            unset($item['bumdes_detail']);

            $lembaga = LembagaDesa::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            if ($bumdesData && $lembaga->jenis_lembaga === 'BUMDes') {
                BumdesDetail::updateOrCreate(
                    ['lembaga_id' => $lembaga->id],
                    $bumdesData
                );
            }
        }
    }
}
