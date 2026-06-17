<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'plans')) {
                $table->longText('plans')->nullable()->after('active');
            }
            if (!Schema::hasColumn('activities', 'agama')) {
                $table->longText('agama')->nullable()->after('plans');
            }
            if (!Schema::hasColumn('activities', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('agama');
            }
            if (!Schema::hasColumn('activities', 'status')) {
                $table->string('status')->default('pending')->after('views');
            }
            if (!Schema::hasColumn('activities', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('activities', 'prompt')) {
                $table->text('prompt')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('activities', 'notes')) {
                $table->text('notes')->nullable()->after('prompt');
            }
            if (!Schema::hasColumn('activities', 'creator')) {
                $table->string('creator')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $columns = ['plans', 'agama', 'views', 'status', 'created_by', 'prompt', 'notes', 'creator'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('activities', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
