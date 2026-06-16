<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('verified_at')->nullable()->after('phone');
        });

        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code', 10);
            $table->enum('channel', ['whatsapp', 'telegram', 'email']);
            $table->dateTime('expires_at');
            $table->boolean('used')->default(false);
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verified_at');
        });
    }
};
