<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('section_teacher')) {
            Schema::create('section_teacher', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('section_ID');
                $table->unsignedBigInteger('teacher_ID');
                $table->timestamps();

                $table->unique(['section_ID', 'teacher_ID']);

                $table->foreign('section_ID')->references('section_ID')->on('sections')->cascadeOnDelete();
                $table->foreign('teacher_ID')->references('teacher_ID')->on('teachers')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('section_teacher');
    }
};
