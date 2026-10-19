<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('apbdes')) {
            Schema::table('apbdes', function (Blueprint $table) {
                if (!Schema::hasColumn('apbdes', 'jenis')) {
                    $table->string('jenis')->default('belanja')->after('tahun')->index(); // pendapatan, belanja, pembiayaan
                }
                if (!Schema::hasColumn('apbdes', 'kategori')) {
                    $table->string('kategori')->nullable()->after('jenis'); // PENDAPATAN TRANSFER, PELAKSANAAN PEMBANGUNAN DESA, dll.
                }
            });

            // Lepaskan unique constraint (tahun, nama_bidang) jika ada agar bisa memasukkan item dengan nama fleksibel
            try {
                Schema::table('apbdes', function (Blueprint $table) {
                    $table->dropUnique(['tahun', 'nama_bidang']);
                });
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('apbdes')) {
            Schema::table('apbdes', function (Blueprint $table) {
                if (Schema::hasColumn('apbdes', 'kategori')) {
                    $table->dropColumn('kategori');
                }
                if (Schema::hasColumn('apbdes', 'jenis')) {
                    $table->dropColumn('jenis');
                }
            });
        }
    }
};
