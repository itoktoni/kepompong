<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id('addon_id');
            $table->integer('addon_id_user');
            $table->string('addon_nama');
            $table->text('addon_desc')->nullable();
            $table->integer('addon_harga')->default(0);
            $table->string('addon_age', 20)->nullable();
            $table->string('addon_age_label', 30)->nullable();
            $table->json('addon_ages')->nullable();
            $table->json('addon_agama')->nullable();
            $table->json('addon_plans')->nullable();
            $table->string('addon_bg', 20)->nullable();
            $table->string('addon_icon', 80)->nullable();
            $table->boolean('addon_active')->default(true);
            $table->dateTime('addon_created_at')->nullable();
            $table->dateTime('addon_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
