<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE verification_codes MODIFY COLUMN channel ENUM('whatsapp','telegram','email','log') NOT NULL DEFAULT 'email'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE verification_codes MODIFY COLUMN channel ENUM('whatsapp','telegram','email') NOT NULL DEFAULT 'email'");
    }
};
