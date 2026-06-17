<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->text('idea_agama')->nullable();
            $table->text('idea_ages')->nullable();
            $table->text('idea_skills')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->dropColumn(['idea_agama', 'idea_ages', 'idea_skills']);
        });
    }
};
