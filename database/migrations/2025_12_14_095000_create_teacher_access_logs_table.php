<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_access_logs')) {
            Schema::create('teacher_access_logs', function (Blueprint $table) {
                $table->id('log_ID');
                $table->unsignedBigInteger('account_ID');
                $table->unsignedBigInteger('teacher_ID')->nullable();
                $table->enum('action', ['Login', 'Logout']);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['account_ID', 'created_at']);
                $table->foreign('account_ID')->references('account_ID')->on('accounts')->cascadeOnDelete();
                $table->foreign('teacher_ID')->references('teacher_ID')->on('teachers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_access_logs');
    }
};
