<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            VillageDataSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@kelurahan-semampir.go.id'],
            [
                'name' => 'Administrator Kelurahan',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@kelurahan-semampir.go.id'],
            [
                'name' => 'Staf Pelayanan Kelurahan',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]
        );
    }
}
