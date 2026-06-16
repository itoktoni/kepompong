<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->unsignedBigInteger('schedule_id_anak')->index();
            $table->string('schedule_label');
            $table->string('schedule_time')->nullable();
            $table->boolean('schedule_done')->default(false);
            $table->date('schedule_date')->nullable();
            $table->dateTime('schedule_created_at')->nullable();
            $table->dateTime('schedule_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
