<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id('skill_id');
            $table->unsignedBigInteger('skill_id_anak')->index();
            $table->string('skill_key');
            $table->string('skill_emoji')->nullable();
            $table->string('skill_title');
            $table->string('skill_pilar')->nullable();
            $table->integer('skill_progress')->default(0);
            $table->string('skill_color')->nullable();
            $table->dateTime('skill_created_at')->nullable();
            $table->dateTime('skill_updated_at')->nullable();

            $table->unique(['skill_id_anak', 'skill_key'], 'sk_anak_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
