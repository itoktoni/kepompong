<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id('challenge_id');
            $table->unsignedBigInteger('challenge_id_anak')->index();
            $table->string('challenge_category');
            $table->string('challenge_title');
            $table->string('challenge_emoji')->nullable();
            $table->integer('challenge_points')->default(0);
            $table->string('challenge_status')->default('pending');
            $table->date('challenge_date')->nullable();
            $table->json('challenge_meta')->nullable();
            $table->dateTime('challenge_created_at')->nullable();
            $table->dateTime('challenge_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
