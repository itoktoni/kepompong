<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worksheets', function (Blueprint $table) {
            $table->integer('worksheet_creator_id')->nullable()->after('worksheet_active');
            $table->integer('worksheet_addon_id')->nullable()->after('worksheet_creator_id');
        });
    }

    public function down(): void
    {
        Schema::table('worksheets', function (Blueprint $table) {
            $table->dropColumn(['worksheet_creator_id', 'worksheet_addon_id']);
        });
    }
};
