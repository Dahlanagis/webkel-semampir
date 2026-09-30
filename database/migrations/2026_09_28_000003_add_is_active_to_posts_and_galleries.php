<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts') && !Schema::hasColumn('posts', 'is_active')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_slider');
            });
        }

        if (Schema::hasTable('galleries') && !Schema::hasColumn('galleries', 'is_active')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('show_on_homepage');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'is_active')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasTable('galleries') && Schema::hasColumn('galleries', 'is_active')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
