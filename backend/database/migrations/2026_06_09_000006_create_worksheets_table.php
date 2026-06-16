<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worksheets', function (Blueprint $table) {
            $table->id('worksheet_id');
            $table->unsignedBigInteger('worksheet_id_anak')->index();
            $table->string('worksheet_type');
            $table->json('worksheet_data')->nullable();
            $table->date('worksheet_date')->nullable();
            $table->dateTime('worksheet_created_at')->nullable();
            $table->dateTime('worksheet_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worksheets');
    }
};
