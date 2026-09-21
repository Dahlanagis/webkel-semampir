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
        Schema::table('service_types', function (Blueprint $table) {
            $table->string('pdf_document')->nullable()->after('description');
            $table->text('sop_description')->nullable()->after('pdf_document');
            $table->string('operational_hours')->nullable()->after('sop_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn(['pdf_document', 'sop_description', 'operational_hours']);
        });
    }
};
