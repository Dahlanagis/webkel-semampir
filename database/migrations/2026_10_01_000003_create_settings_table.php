<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });

            // Seed initial village_profile data if exists
            $profilePath = storage_path('app/village_profile.json');
            if (!File::exists($profilePath)) {
                $profilePath = base_path('storage/app/village_profile.json');
            }
            if (File::exists($profilePath)) {
                $profileContent = File::get($profilePath);
                if (!empty($profileContent)) {
                    DB::table('settings')->insert([
                        'key' => 'village_profile',
                        'value' => $profileContent,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Seed initial system_settings data if exists
            $sysPath = storage_path('app/system_settings.json');
            if (!File::exists($sysPath)) {
                $sysPath = base_path('storage/app/system_settings.json');
            }
            if (File::exists($sysPath)) {
                $sysContent = File::get($sysPath);
                if (!empty($sysContent)) {
                    DB::table('settings')->insert([
                        'key' => 'system_settings',
                        'value' => $sysContent,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
