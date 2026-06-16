<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_activities', function (Blueprint $table) {
            $table->id('skill_activity_id');
            $table->unsignedBigInteger('skill_activity_id_skill')->index();
            $table->string('skill_activity_title');
            $table->string('skill_activity_emoji')->nullable();
            $table->string('skill_activity_feature')->nullable();
            $table->string('skill_activity_date')->nullable();
            $table->boolean('skill_activity_completed')->default(false);
            $table->dateTime('skill_activity_created_at')->nullable();
            $table->dateTime('skill_activity_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_activities');
    }
};
