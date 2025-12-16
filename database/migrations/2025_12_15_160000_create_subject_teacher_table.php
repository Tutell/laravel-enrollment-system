<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('subject_ID');
            $table->unsignedBigInteger('teacher_ID');
            $table->timestamps();
            $table->unique(['subject_ID', 'teacher_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
    }
};

