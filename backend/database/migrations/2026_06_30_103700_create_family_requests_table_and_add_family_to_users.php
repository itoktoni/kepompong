<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_requests', function (Blueprint $table) {
            $table->id('family_request_id');
            $table->integer('family_request_id_user');
            $table->integer('family_request_id_target');
            $table->string('family_request_email');
            $table->enum('family_request_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('family_request_label')->nullable();
            $table->dateTime('family_request_created_at')->nullable();
            $table->dateTime('family_request_updated_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('family')->nullable()->after('user_agama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_requests');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('family');
        });
    }
};
