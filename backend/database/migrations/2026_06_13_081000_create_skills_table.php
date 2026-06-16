<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_skills', function (Blueprint $table) {
            $table->id('skill_id');
            $table->string('skill_key', 80)->unique();
            $table->string('skill_emoji', 10);
            $table->string('skill_title');
            $table->text('skill_desc')->nullable();
            $table->json('skill_ages')->nullable();
            $table->json('skill_pilars')->nullable();
            $table->json('skill_evaluasi')->nullable();
            $table->string('skill_color', 20)->nullable();
            $table->string('skill_bg', 20)->nullable();
            $table->integer('skill_sort_order')->default(0);
            $table->boolean('skill_active')->default(true);
            $table->dateTime('skill_created_at')->nullable();
            $table->dateTime('skill_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_skills');
    }
};
