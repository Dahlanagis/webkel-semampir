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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('time_start', 20)->nullable(); // e.g. "08:30"
            $table->string('time_end', 20)->nullable();   // e.g. "11:30" or "Selesai"
            $table->string('location');                  // e.g. "Balai Pertemuan Kelurahan Semampir"
            $table->string('organizer')->nullable();      // e.g. "Pemerintah Kelurahan Semampir", "TP PKK"
            $table->string('coordinator')->nullable();    // e.g. "Bpk. Sekretaris / Kasi Trantib"
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
