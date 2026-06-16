<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anak', function (Blueprint $table) {
            $table->id('anak_id');
            $table->unsignedBigInteger('anak_id_user')->nullable()->index();
            $table->string('anak_nama');
            $table->integer('anak_umur')->nullable();
            $table->string('anak_gender')->nullable();
            $table->integer('anak_tanggal_lahir')->nullable();
            $table->integer('anak_bulan_lahir')->nullable();
            $table->integer('anak_tahun_lahir')->nullable();
            $table->string('anak_emoji')->default('👶');
            $table->string('anak_avatar')->nullable();
            $table->json('anak_settings')->nullable();
            $table->dateTime('anak_created_at')->nullable();
            $table->dateTime('anak_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak');
    }
};
