<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->integer('idea_qty')->default(10)->after('idea_skills');
        });
    }

    public function down(): void
    {
        Schema::table('idea', function (Blueprint $table) {
            $table->dropColumn('idea_qty');
        });
    }
};
