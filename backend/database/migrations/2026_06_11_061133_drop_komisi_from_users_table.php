<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('komisi');
        });
    }

    public function down(): Void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('komisi')->default(0)->after('affiliate_reff');
        });
    }
};
