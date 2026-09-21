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
        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('name'); // e.g. "Tahun 2023"
            $table->string('file_path');
            $table->timestamps();
        });

        // Drop the old pdf_document and category columns from documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['pdf_document', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('pdf_document')->nullable();
            $table->string('category')->nullable();
        });

        Schema::dropIfExists('document_files');
    }
};
