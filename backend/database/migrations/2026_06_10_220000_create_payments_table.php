<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('payment_plan_id');
            $table->string('payment_order_code')->unique();
            $table->integer('payment_amount');
            $table->integer('payment_discount')->default(0);
            $table->integer('payment_total');
            $table->text('payment_qris_string')->nullable();
            $table->text('payment_notes')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'expired', 'cancelled', 'rejected'])->default('pending');
            $table->string('payment_method')->default('qris');
            $table->dateTime('payment_paid_at')->nullable();
            $table->dateTime('payment_expired_at');
            $table->dateTime('payment_created_at')->nullable();
            $table->dateTime('payment_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
