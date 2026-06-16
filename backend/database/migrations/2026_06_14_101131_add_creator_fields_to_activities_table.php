<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->integer('created_by')->default(1)->after('status');
            $table->text('prompt')->nullable()->after('created_by');
            $table->text('notes')->nullable()->after('prompt');
            $table->string('creator')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'prompt', 'notes', 'creator']);
        });
    }
};
