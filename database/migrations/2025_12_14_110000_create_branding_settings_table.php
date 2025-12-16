<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branding_settings', function (Blueprint $table) {
            $table->id('branding_ID');
            $table->string('system_name')->default('Laravel');
            $table->string('welcome_message')->default('Welcome To Laravel');
            $table->string('subtext')->nullable();
            $table->string('school_name')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->text('core_values')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_settings');
    }
};

