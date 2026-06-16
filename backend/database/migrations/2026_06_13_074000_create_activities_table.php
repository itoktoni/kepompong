<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('desc')->nullable();
            $table->string('image')->nullable();
            $table->text('moral')->nullable();
            $table->json('ages')->nullable();
            $table->json('skills')->nullable();
            $table->json('data')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
