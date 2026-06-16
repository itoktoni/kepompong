<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashouts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('amount');
            $table->integer('admin_fee');
            $table->integer('received');
            $table->string('rekening_bank');
            $table->string('rekening_nomor');
            $table->string('rekening_nama');
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashouts');
    }
};
