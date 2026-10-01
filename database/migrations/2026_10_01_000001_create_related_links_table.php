<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('related_links')) {
            Schema::create('related_links', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('url')->default('#');
                $table->string('desc')->nullable();
                $table->text('logo')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed default kemitraan links
            $defaultLinks = [
                [
                    'name' => 'Pemerintah Kabupaten Probolinggo',
                    'url' => 'https://probolinggokab.go.id',
                    'desc' => 'Portal Resmi Pemerintah Kabupaten Probolinggo',
                    'logo' => 'kemitraan/8PqDzUpiMV2pJtnw6RRkzE7QRH0Gthhdlmhs6pnT.png',
                    'order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Diskominfo Kab. Probolinggo',
                    'url' => 'https://diskominfo.probolinggokab.go.id',
                    'desc' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                    'logo' => 'kemitraan/AbNfqxBWSvM48OtGgXF1V2CO23Od9R2LrykyuiNz.png',
                    'order' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Dispendukcapil Kab. Probolinggo',
                    'url' => 'https://dispendukcapil.probolinggokab.go.id',
                    'desc' => 'Dinas Kependudukan dan Pencatatan Sipil',
                    'logo' => 'kemitraan/luNE2cYyAC8gM25HlmZAhkCnCcWPobkB5hS0401V.png',
                    'order' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Bapenda Kab. Probolinggo',
                    'url' => 'https://bapenda.probolinggokab.go.id',
                    'desc' => 'Badan Pendapatan Daerah (PBB-P2 & Pajak Daerah)',
                    'logo' => 'kemitraan/RzhffVYrMgLzU8yDjtOmVnb7LYzVABR5azsqxHL0.png',
                    'order' => 4,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'DLH Kab. Probolinggo',
                    'url' => 'https://dlh.probolinggokab.go.id/',
                    'desc' => 'Dinas Lingkungan Hidup Kabupaten Probolinggo',
                    'logo' => 'kemitraan/TNtVrVuH16HN5lgd3Qe2jQfUhPM70BPOV2985dmN.png',
                    'order' => 5,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('related_links')->insert($defaultLinks);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_links');
    }
};
