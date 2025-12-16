<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('year_levels')) {
            Schema::create('year_levels', function (Blueprint $table) {
                $table->id('year_level_ID');
                $table->unsignedTinyInteger('grade_level')->unique(); // 7–10
                $table->unsignedInteger('student_count')->default(0); // aggregate
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('year_levels');
    }
};
