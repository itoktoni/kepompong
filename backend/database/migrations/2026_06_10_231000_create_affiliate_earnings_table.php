<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_earnings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('from_user_id');
            $table->integer('payment_id')->nullable();
            $table->enum('type', ['register', 'upgrade']);
            $table->integer('amount');
            $table->integer('payment_amount')->nullable();
            $table->integer('commission_rate')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->dateTime('earning_created_at')->nullable();
            $table->dateTime('earning_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_earnings');
    }
};
