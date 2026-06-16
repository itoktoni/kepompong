<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_histories', function (Blueprint $table) {
            $table->id('challenge_history_id');
            $table->unsignedBigInteger('challenge_history_id_anak')->index();
            $table->string('challenge_history_category');
            $table->string('challenge_history_title');
            $table->date('challenge_history_date');
            $table->json('challenge_history_meta')->nullable();
            $table->dateTime('challenge_history_created_at')->nullable();
            $table->dateTime('challenge_history_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_histories');
    }
};
