<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branding_audits', function (Blueprint $table) {
            $table->id('audit_ID');
            $table->unsignedBigInteger('branding_ID')->nullable();
            $table->unsignedBigInteger('actor_account_ID')->nullable();
            $table->string('action');
            $table->json('changes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_audits');
    }
};

