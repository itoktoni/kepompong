<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->id('checklist_id');
            $table->unsignedBigInteger('checklist_id_anak')->index();
            $table->string('checklist_title');
            $table->json('checklist_items')->nullable();
            $table->date('checklist_date')->nullable();
            $table->dateTime('checklist_created_at')->nullable();
            $table->dateTime('checklist_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
