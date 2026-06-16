<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id('evaluation_id');
            $table->unsignedBigInteger('evaluation_id_anak')->index();
            $table->string('evaluation_skill_key');
            $table->string('evaluation_skill_title')->nullable();
            $table->string('evaluation_pilar')->nullable();
            $table->integer('evaluation_points')->default(0);
            $table->integer('evaluation_max_points')->default(10);
            $table->text('evaluation_notes')->nullable();
            $table->dateTime('evaluation_created_at')->nullable();
            $table->dateTime('evaluation_updated_at')->nullable();

            $table->unique(['evaluation_id_anak', 'evaluation_skill_key'], 'ev_anak_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
