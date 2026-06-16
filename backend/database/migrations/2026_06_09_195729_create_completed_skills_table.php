<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('completed_skills', function (Blueprint $table) {
            $table->id('completed_skill_id');
            $table->unsignedBigInteger('completed_skill_id_anak')->index();
            $table->string('completed_skill_key');
            $table->string('completed_skill_emoji')->nullable();
            $table->string('completed_skill_title');
            $table->string('completed_skill_pilar')->nullable();
            $table->string('completed_skill_color')->nullable();
            $table->timestamp('completed_skill_completed_at')->nullable();
            $table->dateTime('completed_skill_created_at')->nullable();
            $table->dateTime('completed_skill_updated_at')->nullable();

            $table->unique(['completed_skill_id_anak', 'completed_skill_key'], 'cs_anak_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('completed_skills');
    }
};
