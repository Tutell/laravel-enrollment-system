<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id('academic_year_ID');
                $table->string('school_year', 9);
                $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer']);
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
