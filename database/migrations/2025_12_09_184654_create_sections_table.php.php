<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id('section_ID');

                $table->unsignedBigInteger('teacher_ID')->nullable();

                $table->string('section_name', 50);
                $table->integer('grade_level');
                $table->integer('capacity');

                $table->foreign('teacher_ID')->references('teacher_ID')->on('teachers')->nullOnDelete();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
