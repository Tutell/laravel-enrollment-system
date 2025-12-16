<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id('course_ID');

                $table->unsignedBigInteger('subject_ID');
                $table->unsignedBigInteger('teacher_ID');
                $table->unsignedBigInteger('academic_year_ID');

                $table->string('course_code', 20)->unique();
                $table->string('schedule', 100);
                $table->string('room_number', 10);
                $table->integer('max_capacity');

                $table->foreign('subject_ID')->references('subject_ID')->on('subjects')->cascadeOnDelete();
                $table->foreign('teacher_ID')->references('teacher_ID')->on('teachers');
                $table->foreign('academic_year_ID')->references('academic_year_ID')->on('academic_years');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
