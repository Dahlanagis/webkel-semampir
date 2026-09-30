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
        if (!Schema::hasTable('apbdes')) {
            Schema::create('apbdes', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('tahun')->index(); // Format YYYY (2000 - 2099)
                $table->string('kode_rekening')->nullable();
                $table->string('nama_bidang');
                $table->decimal('anggaran', 15, 2)->default(0);
                $table->decimal('realisasi', 15, 2)->default(0);
                $table->decimal('persentase', 5, 2)->default(0);
                $table->text('deskripsi')->nullable();
                $table->timestamps();

                // Unique constraint: Kombinasi tahun dan nama bidang tidak boleh ganda
                $table->unique(['tahun', 'nama_bidang']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apbdes');
    }
};
