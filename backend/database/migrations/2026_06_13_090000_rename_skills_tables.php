<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('skills', 'skills_anak');
        Schema::rename('master_skills', 'skills');
    }

    public function down(): void
    {
        Schema::rename('skills', 'master_skills');
        Schema::rename('skills_anak', 'skills');
    }
};
