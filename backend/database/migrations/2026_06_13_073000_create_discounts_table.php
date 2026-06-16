<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id('discount_id');
            $table->string('discount_code', 50)->unique();
            $table->string('discount_nama');
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->integer('discount_value');
            $table->integer('discount_min_transaction')->default(0);
            $table->integer('discount_max_amount')->nullable();
            $table->boolean('discount_active')->default(true);
            $table->dateTime('discount_start')->nullable();
            $table->dateTime('discount_end')->nullable();
            $table->unsignedBigInteger('discount_created_by')->nullable();
            $table->unsignedBigInteger('discount_updated_by')->nullable();
            $table->unsignedBigInteger('discount_deleted_by')->nullable();
            $table->dateTime('discount_created_at')->nullable();
            $table->dateTime('discount_updated_at')->nullable();
            $table->softDeletes('discount_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
