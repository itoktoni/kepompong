<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscribe', function (Blueprint $table) {

            $table->integer('subscribe_id', true);
            $table->integer('subscribe_id_user');
            $table->integer('subscribe_harga')->nullable();
            $table->integer('subscribe_discount')->nullable();
            $table->integer('subscribe_total')->nullable();
            $table->integer('subscribe_id_plan');
            $table->integer('subsribe_value');
            $table->dateTime('subscribe_trial_at')->nullable();
            $table->dateTime('subscribe_start_at')->nullable();
            $table->dateTime('subscribe_end_at')->nullable();
            $table->dateTime('subscribe_canceled_at')->nullable();
            $table->dateTime('subscribe_created_at')->nullable();
            $table->dateTime('subscribe_updated_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribe');
    }
};
