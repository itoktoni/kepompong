<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_histories', function (Blueprint $table) {
            $table->id('schedule_history_id');
            $table->unsignedBigInteger('schedule_history_id_schedule')->index();
            $table->unsignedBigInteger('schedule_history_id_anak')->index();
            $table->date('schedule_history_date');
            $table->string('schedule_history_time')->nullable();
            $table->dateTime('schedule_history_created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_histories');
    }
};
