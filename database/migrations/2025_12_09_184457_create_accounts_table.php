<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id('account_ID');
                $table->string('Username', 50)->unique();
                $table->string('Password_Hash', 255);
                $table->string('Email', 100)->unique();
                $table->enum('role', ['student', 'teacher', 'admin']);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
