<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->renameColumn('idea_moral', 'idea_informasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->renameColumn('idea_informasi', 'idea_moral');
        });
    }
};
