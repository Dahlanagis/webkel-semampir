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
            if (!Schema::hasColumn('service_types', 'icon')) {
                $table->string('icon')->default('📜')->after('code');
            }
            if (!Schema::hasColumn('service_types', 'badge_label')) {
                $table->string('badge_label')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('service_types', 'order')) {
                $table->integer('order')->default(0)->after('badge_label');
            }
            if (!Schema::hasColumn('service_types', 'show_on_homepage')) {
                $table->boolean('show_on_homepage')->default(true)->after('order');
            }
            if (!Schema::hasColumn('service_types', 'is_online_request')) {
                $table->boolean('is_online_request')->default(true)->after('show_on_homepage');
            }
            if (!Schema::hasColumn('service_types', 'action_url')) {
                $table->string('action_url')->nullable()->after('is_online_request');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn([
                'icon',
                'badge_label',
                'order',
                'show_on_homepage',
                'is_online_request',
                'action_url',
            ]);
        });
    }
};
