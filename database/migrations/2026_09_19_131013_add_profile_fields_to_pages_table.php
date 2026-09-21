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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('category')->default('profile')->after('id');
            $table->string('subtitle')->nullable()->after('title');
            $table->string('badge_text')->nullable()->after('subtitle');
            $table->enum('type', ['standard', 'sotk'])->default('standard')->after('slug');
            $table->string('banner_image')->nullable()->after('content');
            $table->integer('order')->default(0)->after('banner_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['category', 'subtitle', 'badge_text', 'type', 'banner_image', 'order']);
        });
    }
};
