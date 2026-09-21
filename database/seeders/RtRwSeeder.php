<?php

namespace Database\Seeders;

use App\Models\RtRw;
use Illuminate\Database\Seeder;

class RtRwSeeder extends Seeder
{
    public function run(): void
    {
        // Data RW
        $rwData = [
            ['type' => 'RW', 'number' => '001', 'head_name' => 'H. Moh. Salim', 'head_phone' => '081230001001', 'address' => 'Dusun Krajan', 'total_kk' => 120],
            ['type' => 'RW', 'number' => '002', 'head_name' => 'Suryadi', 'head_phone' => '081230001002', 'address' => 'Dusun Karanganyar', 'total_kk' => 95],
            ['type' => 'RW', 'number' => '003', 'head_name' => 'Abdul Ghani', 'head_phone' => '081230001003', 'address' => 'Dusun Sumber Wringin', 'total_kk' => 80],
        ];

        // Data RT
        $rtData = [
            // RW 001 - Dusun Krajan
            ['type' => 'RT', 'number' => '001', 'head_name' => 'Mulyono', 'head_phone' => '081231001001', 'address' => 'Dusun Krajan RT 01', 'total_kk' => 40],
            ['type' => 'RT', 'number' => '002', 'head_name' => 'Slamet Riyadi', 'head_phone' => '081231001002', 'address' => 'Dusun Krajan RT 02', 'total_kk' => 38],
            ['type' => 'RT', 'number' => '003', 'head_name' => 'Supriyadi', 'head_phone' => '081231001003', 'address' => 'Dusun Krajan RT 03', 'total_kk' => 42],
            // RW 002 - Dusun Karanganyar
            ['type' => 'RT', 'number' => '004', 'head_name' => 'Haryanto', 'head_phone' => '081231002001', 'address' => 'Dusun Karanganyar RT 01', 'total_kk' => 32],
            ['type' => 'RT', 'number' => '005', 'head_name' => 'Wawan Setiawan', 'head_phone' => '081231002002', 'address' => 'Dusun Karanganyar RT 02', 'total_kk' => 30],
            ['type' => 'RT', 'number' => '006', 'head_name' => 'Darmawan', 'head_phone' => '081231002003', 'address' => 'Dusun Karanganyar RT 03', 'total_kk' => 33],
            // RW 003 - Dusun Sumber Wringin
            ['type' => 'RT', 'number' => '007', 'head_name' => 'Usman Hakim', 'head_phone' => '081231003001', 'address' => 'Dusun Sumber Wringin RT 01', 'total_kk' => 28],
            ['type' => 'RT', 'number' => '008', 'head_name' => 'Fajar Nugroho', 'head_phone' => '081231003002', 'address' => 'Dusun Sumber Wringin RT 02', 'total_kk' => 25],
            ['type' => 'RT', 'number' => '009', 'head_name' => 'Romadhon', 'head_phone' => '081231003003', 'address' => 'Dusun Sumber Wringin RT 03', 'total_kk' => 27],
        ];

        foreach (array_merge($rwData, $rtData) as $data) {
            RtRw::create($data);
        }
    }
}
