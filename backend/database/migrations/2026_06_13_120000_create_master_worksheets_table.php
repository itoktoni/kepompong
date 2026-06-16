<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('worksheets', 'worksheets_anak');

        Schema::create('worksheets', function (Blueprint $table) {
            $table->id('worksheet_id');
            $table->string('worksheet_key', 80)->unique();
            $table->string('worksheet_icon', 80)->nullable();
            $table->string('worksheet_title');
            $table->text('worksheet_desc')->nullable();
            $table->string('worksheet_age', 20)->nullable();
            $table->string('worksheet_age_label', 30)->nullable();
            $table->json('worksheet_ages')->nullable();
            $table->json('worksheet_skills')->nullable();
            $table->json('worksheet_agama')->nullable();
            $table->json('worksheet_plans')->nullable();
            $table->string('worksheet_bg', 20)->nullable();
            $table->string('worksheet_icon_color', 20)->nullable();
            $table->boolean('worksheet_is_api')->default(false);
            $table->integer('worksheet_sort_order')->default(0);
            $table->boolean('worksheet_active')->default(true);
            $table->dateTime('worksheet_created_at')->nullable();
            $table->dateTime('worksheet_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worksheets');
        Schema::rename('worksheets_anak', 'worksheets');
    }
};
