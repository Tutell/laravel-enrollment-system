<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_audits', function (Blueprint $table) {
            $table->id('audit_ID');
            $table->unsignedBigInteger('student_ID')->nullable();
            $table->unsignedBigInteger('actor_account_ID')->nullable();
            $table->string('action', 50);
            $table->string('reason', 255)->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();

            $table->foreign('student_ID')->references('student_ID')->on('students')->cascadeOnDelete();
            $table->foreign('actor_account_ID')->references('account_ID')->on('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_audits');
    }
};

