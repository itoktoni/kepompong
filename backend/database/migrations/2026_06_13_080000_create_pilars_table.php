<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilars', function (Blueprint $table) {
            $table->id('pilar_id');
            $table->string('pilar_key', 50)->unique();
            $table->string('pilar_emoji', 10);
            $table->string('pilar_title');
            $table->string('pilar_subtitle')->nullable();
            $table->string('pilar_color', 20)->nullable();
            $table->string('pilar_bg', 20)->nullable();
            $table->json('pilar_ages')->nullable();
            $table->integer('pilar_sort_order')->default(0);
            $table->boolean('pilar_active')->default(true);
            $table->dateTime('pilar_created_at')->nullable();
            $table->dateTime('pilar_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilars');
    }
};
