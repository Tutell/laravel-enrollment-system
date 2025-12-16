<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_audits', function (Blueprint $table) {
            $table->id('audit_ID');
            $table->unsignedBigInteger('enrollment_ID')->nullable();
            $table->unsignedBigInteger('processed_by_account_ID')->nullable();
            $table->string('action', 50);
            $table->json('changes')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_ID')->references('enrollment_ID')->on('enrollment')->cascadeOnDelete();
            $table->foreign('processed_by_account_ID')->references('account_ID')->on('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_audits');
    }
};
