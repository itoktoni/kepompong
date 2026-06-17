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
        Schema::create('idea', function (Blueprint $table) {
            $table->integer('idea_id', true);
            $table->string('idea_nama')->nullable();
            $table->text('idea_keterangan')->nullable();
            $table->string('idea_moral')->nullable();
            $table->string('idea_type')->nullable();
            $table->string('idea_creator')->nullable();
            $table->date('idea_tanggal')->nullable();
            $table->string('idea_implementor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idea');
    }
};
