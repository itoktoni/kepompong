<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            if (Schema::hasColumn('idea', 'idea_moral')) {
                $table->renameColumn('idea_moral', 'idea_informasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            if (Schema::hasColumn('idea', 'idea_informasi')) {
                $table->renameColumn('idea_informasi', 'idea_moral');
            }
        });
    }
};
