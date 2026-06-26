<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->text('idea_informasi')->nullable()->change();
            $table->text('idea_prompt')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->string('idea_informasi')->nullable()->change();
            $table->string('idea_prompt')->nullable()->change();
        });
    }
};
