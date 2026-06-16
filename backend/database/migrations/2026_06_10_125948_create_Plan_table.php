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
        Schema::create('plan', function (Blueprint $table) {

            $table->integer('plan_id', true);
            $table->string('plan_nama')->nullable();
            $table->text('plan_keterangan')->nullable();
            $table->integer('plan_harga')->nullable();
            $table->integer('plan_value')->nullable();
            $table->integer('plan_status')->nullable();
            $table->integer('plan_fee')->nullable();
            $table->integer('plan_recomended')->nullable();
            $table->string('plan_color')->nullable();
            $table->string('plan_periode')->nullable();
            $table->string('plan_interval')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan');
    }
};
