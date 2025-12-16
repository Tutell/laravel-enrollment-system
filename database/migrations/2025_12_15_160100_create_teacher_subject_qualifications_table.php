<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teacher_subject_qualifications', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('teacher_ID');
            $table->unsignedBigInteger('subject_ID');
            $table->timestamps();
            $table->unique(['teacher_ID', 'subject_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subject_qualifications');
    }
};

