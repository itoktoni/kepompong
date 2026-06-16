<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('activity_stats');
        Schema::dropIfExists('activity_loves');
        Schema::dropIfExists('activity_views');

        Schema::table('activities', function (Blueprint $table) {
            $table->integer('views')->default(0)->after('active');
            $table->enum('status', ['pending', 'review', 'approved', 'rejected'])->default('approved')->after('views');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['views', 'status']);
        });
    }
};
