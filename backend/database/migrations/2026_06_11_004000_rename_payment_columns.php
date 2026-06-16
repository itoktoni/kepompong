<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('id', 'payment_id');
            $table->renameColumn('user_id', 'payment_id_user');
            $table->renameColumn('payment_plan_id', 'payment_id_plan');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_id', 'id');
            $table->renameColumn('payment_id_user', 'user_id');
            $table->renameColumn('payment_id_plan', 'payment_plan_id');
        });
    }
};
