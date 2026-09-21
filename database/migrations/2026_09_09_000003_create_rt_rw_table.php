<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rt_rw', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['RT', 'RW']);
            $table->string('number', 10);
            $table->string('head_name');
            $table->string('head_phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->unsignedInteger('total_kk')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rt_rw');
    }
};
