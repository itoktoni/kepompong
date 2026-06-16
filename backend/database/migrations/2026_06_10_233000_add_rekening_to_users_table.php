<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rekening_nama')->nullable()->after('komisi');
            $table->string('rekening_bank')->nullable()->after('rekening_nama');
            $table->string('rekening_nomor')->nullable()->after('rekening_bank');
            $table->string('rekening_screenhoot')->nullable()->after('rekening_nomor');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rekening_nama', 'rekening_bank', 'rekening_nomor', 'rekening_screenhoot']);
        });
    }
};
