<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id('grade_ID');

                $table->unsignedBigInteger('enrollment_ID');

                $table->string('type', 50);
                $table->decimal('score', 5, 2);
                $table->decimal('weight', 3, 2);
                $table->dateTime('date_recorded');

                $table->foreign('enrollment_ID')->references('enrollment_ID')->on('enrollment')->cascadeOnDelete();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
