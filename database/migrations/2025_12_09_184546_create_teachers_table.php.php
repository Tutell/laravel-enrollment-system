<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id('teacher_ID');
                $table->unsignedBigInteger('account_ID')->unique();

                $table->string('first_name', 50);
                $table->string('last_name', 50);
                $table->string('contact_number', 20)->nullable();
                $table->string('department', 50);

                $table->foreign('account_ID')->references('account_ID')->on('accounts')->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
