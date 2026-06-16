<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('activity_stats');

        Schema::create('activity_views', function (Blueprint $table) {
            $table->id('view_id');
            $table->unsignedBigInteger('view_id_activity');
            $table->unsignedBigInteger('view_id_user');
            $table->dateTime('view_created_at')->nullable();

            $table->unique(['view_id_activity', 'view_id_user']);
            $table->index('view_id_activity');
        });

        Schema::create('activity_loves', function (Blueprint $table) {
            $table->id('love_id');
            $table->unsignedBigInteger('love_id_activity');
            $table->unsignedBigInteger('love_id_user');
            $table->dateTime('love_created_at')->nullable();

            $table->unique(['love_id_activity', 'love_id_user']);
            $table->index('love_id_activity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_views');
        Schema::dropIfExists('activity_loves');
    }
};
