<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_audits', function (Blueprint $table) {
            $table->id('audit_ID');
            $table->unsignedBigInteger('actor_account_ID')->nullable();
            $table->unsignedBigInteger('target_account_ID')->nullable();
            $table->string('action', 50);
            $table->json('changes')->nullable();
            $table->timestamps();

            $table->foreign('actor_account_ID')->references('account_ID')->on('accounts')->nullOnDelete();
            $table->foreign('target_account_ID')->references('account_ID')->on('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_audits');
    }
};
