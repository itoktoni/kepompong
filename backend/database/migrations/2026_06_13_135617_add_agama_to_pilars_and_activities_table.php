<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilars', function (Blueprint $table) {
            $table->json('pilar_agama')->nullable()->after('pilar_plans');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->json('agama')->nullable()->after('plans');
        });
    }

    public function down(): void
    {
        Schema::table('pilars', function (Blueprint $table) {
            $table->dropColumn('pilar_agama');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('agama');
        });
    }
};
