<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enrollment')) {
            Schema::create('enrollment', function (Blueprint $table) {
                $table->id('enrollment_ID');

                $table->unsignedBigInteger('student_ID');
                $table->unsignedBigInteger('course_ID');

                $table->date('enrollment_date');
                $table->enum('status', ['Pending', 'Enrolled', 'Dropped']);

                $table->foreign('student_ID')->references('student_ID')->on('students')->cascadeOnDelete();
                $table->foreign('course_ID')->references('course_ID')->on('courses')->cascadeOnDelete();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment');
    }
};
