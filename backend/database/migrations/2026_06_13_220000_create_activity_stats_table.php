<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_stats', function (Blueprint $table) {
            $table->id('stat_id');
            $table->unsignedBigInteger('stat_id_activity');
            $table->unsignedBigInteger('stat_id_user');
            $table->enum('stat_tipe', ['view', 'love'])->default('view');
            $table->dateTime('stat_created_at')->nullable();

            $table->unique(['stat_id_activity', 'stat_id_user', 'stat_tipe'], 'activity_user_type_unique');
            $table->index('stat_id_activity');
            $table->index('stat_id_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_stats');
    }
};
