<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator Kelurahan',
                'username' => 'admin',
                'whatsapp' => '081234567890',
                'referral_code' => 'ADM123',
                'password' => Hash::make('Dishub#2026!'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'sukma@gmail.com'],
            [
                'name' => 'Sukma Anggota Staf',
                'username' => 'sukma',
                'whatsapp' => '089876543210',
                'referral_code' => 'SUK202',
                'password' => Hash::make('Dishub#2026!'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );
    }
}
