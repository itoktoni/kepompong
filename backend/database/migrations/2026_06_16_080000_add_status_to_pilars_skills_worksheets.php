<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilars', function (Blueprint $table) {
            $table->string('pilar_status', 20)->default('approved')->after('pilar_active');
        });

        Schema::table('skills_anak', function (Blueprint $table) {
            $table->string('skill_status', 20)->default('approved')->after('skill_color');
        });

        Schema::table('worksheets_anak', function (Blueprint $table) {
            $table->string('worksheet_status', 20)->default('approved')->after('worksheet_date');
        });
    }

    public function down(): void
    {
        Schema::table('pilars', function (Blueprint $table) {
            $table->dropColumn('pilar_status');
        });

        Schema::table('skills_anak', function (Blueprint $table) {
            $table->dropColumn('skill_status');
        });

        Schema::table('worksheets_anak', function (Blueprint $table) {
            $table->dropColumn('worksheet_status');
        });
    }
};
