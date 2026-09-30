<?php

namespace Database\Seeders;

use App\Models\Resident;
use Illuminate\Database\Seeder;

class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        $residents = [
            ['nik' => '3513010101900001', 'name' => 'Ahmad Fauzi', 'gender' => 'L', 'birth_place' => 'Probolinggo', 'birth_date' => '1990-01-01', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Petani', 'address' => 'Jl. Raya Semampir No. 10', 'rt' => '001', 'rw' => '001', 'phone_number' => '081234567001'],
            ['nik' => '3513010202880002', 'name' => 'Siti Nurhaliza', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1988-02-02', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Ibu Rumah Tangga', 'address' => 'Jl. Raya Semampir No. 12', 'rt' => '001', 'rw' => '001', 'phone_number' => '081234567002'],
            ['nik' => '3513010303920003', 'name' => 'Budi Santoso', 'gender' => 'L', 'birth_place' => 'Malang', 'birth_date' => '1992-03-03', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Pedagang', 'address' => 'Dusun Krajan RT 02', 'rt' => '002', 'rw' => '001', 'phone_number' => '081234567003'],
            ['nik' => '3513010404950004', 'name' => 'Dewi Kartini', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1995-04-04', 'religion' => 'Islam', 'marital_status' => 'Belum Kawin', 'occupation' => 'Karyawan Swasta', 'address' => 'Dusun Krajan RT 03', 'rt' => '003', 'rw' => '001', 'phone_number' => '081234567004'],
            ['nik' => '3513010505870005', 'name' => 'Hasan Basri', 'gender' => 'L', 'birth_place' => 'Surabaya', 'birth_date' => '1987-05-05', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Wiraswasta', 'address' => 'Dusun Krajan RT 03', 'rt' => '003', 'rw' => '001', 'phone_number' => '081234567005'],
            ['nik' => '3513010606910006', 'name' => 'Nur Aini', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1991-06-06', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Guru', 'address' => 'Dusun Karanganyar RT 01', 'rt' => '001', 'rw' => '002', 'phone_number' => '081234567006'],
            ['nik' => '3513010707850007', 'name' => 'Rudi Hermawan', 'gender' => 'L', 'birth_place' => 'Pasuruan', 'birth_date' => '1985-07-07', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Supir', 'address' => 'Dusun Karanganyar RT 02', 'rt' => '002', 'rw' => '002'],
            ['nik' => '3513010808930008', 'name' => 'Eko Prasetyo', 'gender' => 'L', 'birth_place' => 'Probolinggo', 'birth_date' => '1993-08-08', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Buruh', 'address' => 'Dusun Karanganyar RT 02', 'rt' => '002', 'rw' => '002'],
            ['nik' => '3513010909960009', 'name' => 'Maya Sari', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1996-09-09', 'religion' => 'Islam', 'marital_status' => 'Belum Kawin', 'occupation' => 'Mahasiswa', 'address' => 'Dusun Karanganyar RT 03', 'rt' => '003', 'rw' => '002'],
            ['nik' => '3513011010880010', 'name' => 'Agus Wahyudi', 'gender' => 'L', 'birth_place' => 'Jember', 'birth_date' => '1988-10-10', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Nelayan', 'address' => 'Dusun Sumber Wringin RT 01', 'rt' => '001', 'rw' => '003', 'phone_number' => '081234567010'],
            ['nik' => '3513011111940011', 'name' => 'Ratna Dewi', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1994-11-11', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Pedagang', 'address' => 'Dusun Sumber Wringin RT 01', 'rt' => '001', 'rw' => '003'],
            ['nik' => '3513011212860012', 'name' => 'Suparman', 'gender' => 'L', 'birth_place' => 'Probolinggo', 'birth_date' => '1986-12-12', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Petani', 'address' => 'Dusun Sumber Wringin RT 02', 'rt' => '002', 'rw' => '003'],
            ['nik' => '3513011301970013', 'name' => 'Indah Permata', 'gender' => 'P', 'birth_place' => 'Malang', 'birth_date' => '1997-01-13', 'religion' => 'Islam', 'marital_status' => 'Belum Kawin', 'occupation' => 'Pelajar', 'address' => 'Dusun Sumber Wringin RT 02', 'rt' => '002', 'rw' => '003'],
            ['nik' => '3513011402890014', 'name' => 'Joko Widodo', 'gender' => 'L', 'birth_place' => 'Probolinggo', 'birth_date' => '1989-02-14', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'PNS', 'address' => 'Dusun Sumber Wringin RT 03', 'rt' => '003', 'rw' => '003', 'phone_number' => '081234567014'],
            ['nik' => '3513011503910015', 'name' => 'Sri Wahyuni', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1991-03-15', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Bidan', 'address' => 'Dusun Krajan RT 01', 'rt' => '001', 'rw' => '001', 'phone_number' => '081234567015'],
            ['nik' => '3513011604830016', 'name' => 'Bambang Sugianto', 'gender' => 'L', 'birth_place' => 'Situbondo', 'birth_date' => '1983-04-16', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Tukang Bangunan', 'address' => 'Dusun Krajan RT 02', 'rt' => '002', 'rw' => '001'],
            ['nik' => '3513011705990017', 'name' => 'Fitri Handayani', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1999-05-17', 'religion' => 'Islam', 'marital_status' => 'Belum Kawin', 'occupation' => 'Mahasiswa', 'address' => 'Dusun Karanganyar RT 01', 'rt' => '001', 'rw' => '002'],
            ['nik' => '3513011806870018', 'name' => 'Maulana Ibrahim', 'gender' => 'L', 'birth_place' => 'Probolinggo', 'birth_date' => '1987-06-18', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Montir', 'address' => 'Dusun Karanganyar RT 03', 'rt' => '003', 'rw' => '002', 'phone_number' => '081234567018'],
            ['nik' => '3513011907930019', 'name' => 'Yuliana Sari', 'gender' => 'P', 'birth_place' => 'Probolinggo', 'birth_date' => '1993-07-19', 'religion' => 'Kristen', 'marital_status' => 'Kawin', 'occupation' => 'Perawat', 'address' => 'Dusun Sumber Wringin RT 01', 'rt' => '001', 'rw' => '003'],
            ['nik' => '3513012008800020', 'name' => 'Susilo Wibowo', 'gender' => 'L', 'birth_place' => 'Probolinggo', 'birth_date' => '1980-08-20', 'religion' => 'Islam', 'marital_status' => 'Kawin', 'occupation' => 'Pensiunan', 'address' => 'Dusun Sumber Wringin RT 03', 'rt' => '003', 'rw' => '003'],
        ];

        foreach ($residents as $data) {
            Resident::create($data);
        }
    }
}
