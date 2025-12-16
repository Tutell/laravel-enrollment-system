<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id('student_ID');
                $table->unsignedBigInteger('account_ID')->unique();
                $table->unsignedBigInteger('section_ID')->nullable();

                $table->string('first_name', 50);
                $table->string('last_name', 50);
                $table->enum('gender', ['Male', 'Female', 'Other']);
                $table->date('birthdate');

                $table->foreign('account_ID')->references('account_ID')->on('accounts')->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
