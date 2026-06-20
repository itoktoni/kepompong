<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addons', function (Blueprint $table) {
            $table->id('user_addon_id');
            $table->integer('user_addon_id_user');
            $table->integer('user_addon_id_addon');
            $table->integer('user_addon_harga')->default(0);
            $table->string('user_addon_status', 20)->default('active');
            $table->dateTime('user_addon_created_at')->nullable();
            $table->dateTime('user_addon_expired_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addons');
    }
};
