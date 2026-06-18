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
        Schema::create('payment_method', function (Blueprint $table) {

            $table->integer('payment_method_id');
            $table->string('payment_method_nama')->nullable();
            $table->string('payment_method_person')->nullable();
            $table->string('payment_method_rekening')->nullable();
            $table->text('payment_method_transfer')->nullable();
            $table->string('payment_method_category')->nullable()->default('bank');
            $table->integer('payment_method_active')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method');
    }
};
