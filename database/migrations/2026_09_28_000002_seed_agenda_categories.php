<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultCategories = [
            [
                'name' => 'Rapat & Kedinasan',
                'slug' => 'rapat-kedinasan',
                'color_code' => 'slate',
                'description' => 'Rapat koordinasi pimpinan, staf, dan aparatur RT/RW.'
            ],
            [
                'name' => 'Sosialisasi & Penyuluhan',
                'slug' => 'sosialisasi-penyuluhan',
                'color_code' => 'blue',
                'description' => 'Penyuluhan hukum, bimbingan teknis, dan sosialisasi program dinas.'
            ],
            [
                'name' => 'Kerja Bakti & Lingkungan',
                'slug' => 'kerja-bakti-lingkungan',
                'color_code' => 'emerald',
                'description' => 'Gotong royong kebersihan saluran, penanaman pohon, dan lingkungan sehat.'
            ],
            [
                'name' => 'Posyandu & Kesehatan',
                'slug' => 'posyandu-kesehatan',
                'color_code' => 'rose',
                'description' => 'Pemeriksaan kesehatan posyandu balita, lansia, dan pencegahan stunting.'
            ],
            [
                'name' => 'Kemasyarakatan & Budaya',
                'slug' => 'kemasyarakatan-budaya',
                'color_code' => 'amber',
                'description' => 'Peringatan hari besar nasional, lomba, dan pembinaan warga.'
            ],
            [
                'name' => 'Keagamaan',
                'slug' => 'keagamaan',
                'color_code' => 'indigo',
                'description' => 'Pengajian rutin, peringatan hari besar keagamaan, dan pembinaan rohani.'
            ],
        ];

        foreach ($defaultCategories as $item) {
            Category::firstOrCreate(
                ['type' => 'agenda', 'slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'color_code' => $item['color_code'],
                    'description' => $item['description']
                ]
            );
        }

        // Hubungkan juga sample agenda kegiatan yang sudah dibuat sebelumnya ke kategori yang sesuai
        $rapat = \App\Models\Agenda::where('slug', 'like', '%rapat%')->first();
        if ($rapat) {
            $cat = Category::where('type', 'agenda')->where('slug', 'rapat-kedinasan')->first();
            if ($cat) {
                $rapat->update(['category_id' => $cat->id]);
            }
        }

        $posyandu = \App\Models\Agenda::where('slug', 'like', '%posyandu%')->first();
        if ($posyandu) {
            $cat = Category::where('type', 'agenda')->where('slug', 'posyandu-kesehatan')->first();
            if ($cat) {
                $posyandu->update(['category_id' => $cat->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Category::where('type', 'agenda')->delete();
    }
};
