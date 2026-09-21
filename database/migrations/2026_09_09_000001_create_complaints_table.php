<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 20)->unique();
            $table->string('resident_name');
            $table->string('nik', 16);
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('subject');
            $table->enum('category', ['infrastruktur', 'pelayanan', 'keamanan', 'kebersihan', 'lainnya'])->default('lainnya');
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'in_review', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
