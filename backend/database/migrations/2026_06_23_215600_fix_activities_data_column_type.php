<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `activities` MODIFY COLUMN `data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `activities` MODIFY COLUMN `data` JSON");
    }
};
