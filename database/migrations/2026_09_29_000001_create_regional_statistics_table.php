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
        Schema::create('regional_statistics', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->default(2026)->index();
            $table->string('nama_kelurahan')->default('Kelurahan Semampir');
            $table->string('nama_kecamatan')->default('Kecamatan Kraksaan');
            $table->string('nama_kabupaten')->default('Kabupaten Probolinggo');
            $table->decimal('luas_wilayah', 8, 2)->default(3.82)->comment('Luas wilayah dalam satuan km²');
            
            // Pembagian Administratif
            $table->unsignedInteger('jumlah_rt')->default(32)->comment('Jumlah Rukun Tetangga (RT)');
            $table->unsignedInteger('jumlah_rw')->default(8)->comment('Jumlah Rukun Warga (RW)');
            
            // Kependudukan & KK
            $table->unsignedInteger('total_penduduk')->default(5000)->comment('Total Jiwa Penduduk Terdaftar');
            $table->decimal('pertumbuhan_penduduk', 5, 2)->default(1.20)->comment('Tingkat pertumbuhan tahunan (%)');
            $table->unsignedInteger('jumlah_laki_laki')->default(4180)->comment('Jumlah penduduk pria');
            $table->unsignedInteger('jumlah_perempuan')->default(4245)->comment('Jumlah penduduk wanita');
            $table->unsignedInteger('jumlah_kk')->default(2640)->comment('Jumlah Kepala Keluarga (KK)');
            
            // Komposisi Kelompok Usia
            $table->unsignedInteger('usia_produktif')->default(5610)->comment('Usia Kerja 15-64 Tahun');
            $table->unsignedInteger('usia_anak')->default(1825)->comment('Usia Anak 0-14 Tahun');
            $table->unsignedInteger('usia_lansia')->default(990)->comment('Usia Lanjut 65+ Tahun');
            
            // Meta Informasi
            $table->string('sumber_data')->default('SIAK Dispendukcapil')->comment('Instansi / Sistem sumber data');
            $table->boolean('is_active')->default(true)->comment('Status aktif data dashboard');
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional_statistics');
    }
};
