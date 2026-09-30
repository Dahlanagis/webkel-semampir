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
        if (!Schema::hasTable('lembaga_desa')) {
            Schema::create('lembaga_desa', function (Blueprint $table) {
                $table->id();
                $table->string('nama_lembaga');
                $table->string('singkatan')->nullable();
                $table->string('slug')->unique();
                $table->enum('jenis_lembaga', ['LKD', 'BUMDes', 'Lembaga Pemerintahan', 'Lembaga Adat'])->default('LKD')->index();
                $table->string('nomor_sk_pendirian')->nullable();
                $table->date('tanggal_sk')->nullable();
                $table->string('dasar_hukum')->nullable();
                $table->string('nama_ketua')->nullable();
                $table->string('kontak')->nullable();
                $table->text('alamat_kantor')->nullable();
                $table->text('deskripsi_profil')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bumdes_detail')) {
            Schema::create('bumdes_detail', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lembaga_id')->constrained('lembaga_desa')->onDelete('cascade')->unique();
                $table->string('nomor_badan_hukum_kemenkumham')->nullable();
                $table->unsignedSmallInteger('tahun_pendirian')->nullable();
                $table->string('npwp_bumdes')->nullable();
                $table->enum('kategori_status', ['Perintis', 'Berkembang', 'Maju', 'Mandiri'])->default('Berkembang');
                $table->decimal('permodalan_awal', 15, 2)->default(0);
                $table->decimal('total_aset', 15, 2)->default(0);
                $table->decimal('omzet_terakhir', 15, 2)->default(0);
                $table->text('daftar_unit_usaha')->nullable();
                $table->string('nama_penasihat')->nullable();
                $table->string('nama_pelaksana_operasional')->nullable();
                $table->string('nama_pengawas')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bumdes_detail');
        Schema::dropIfExists('lembaga_desa');
    }
};
