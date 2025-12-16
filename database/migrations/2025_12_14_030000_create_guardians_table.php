<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id('guardian_ID');

            $table->unsignedBigInteger('student_ID');
            $table->string('full_name', 100);
            $table->string('relationship', 50);
            $table->string('contact_number', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('occupation', 100)->nullable();

            $table->foreign('student_ID')
                ->references('student_ID')
                ->on('students')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
