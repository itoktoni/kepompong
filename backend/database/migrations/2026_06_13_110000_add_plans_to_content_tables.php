<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilars', function (Blueprint $table) {
            $table->json('pilar_plans')->nullable()->after('pilar_ages');
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->json('skill_plans')->nullable()->after('skill_agama');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->json('plans')->nullable()->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('pilars', function (Blueprint $table) {
            $table->dropColumn('pilar_plans');
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('skill_plans');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('plans');
        });
    }
};
