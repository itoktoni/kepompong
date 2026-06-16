<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->integer('plan_coret')->nullable()->after('plan_harga');
        });
    }

    public function down(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->dropColumn('plan_coret');
        });
    }
};
