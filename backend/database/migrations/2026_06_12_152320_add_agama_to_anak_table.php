<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anak', function (Blueprint $table) {
            $table->enum('anak_agama', [
                'islam',
                'kristen_protestan',
                'kristen_katolik',
                'hindu',
                'buddha',
                'konghucu',
            ])->nullable()->after('anak_gender');
        });
    }

    public function down(): void
    {
        Schema::table('anak', function (Blueprint $table) {
            $table->dropColumn('anak_agama');
        });
    }
};
