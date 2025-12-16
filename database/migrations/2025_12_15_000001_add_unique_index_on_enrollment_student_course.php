<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            if (! Schema::hasColumn('enrollment', 'student_ID') || ! Schema::hasColumn('enrollment', 'course_ID')) {
                return;
            }
            $table->unique(['student_ID', 'course_ID'], 'enrollment_student_course_unique');
        });
    }

    public function down(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropUnique('enrollment_student_course_unique');
        });
    }
};
