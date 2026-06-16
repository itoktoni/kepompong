<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->default('info');
            $table->string('icon_color')->default('text-primary');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('url')->nullable();
            $table->string('type')->default('info');
            $table->boolean('read')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
