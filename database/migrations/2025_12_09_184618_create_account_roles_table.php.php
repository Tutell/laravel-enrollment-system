<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('account_roles')) {
            Schema::create('account_roles', function (Blueprint $table) {
                $table->id('account_role_ID');

                $table->unsignedBigInteger('account_ID');
                $table->unsignedBigInteger('role_ID');

                $table->foreign('account_ID')->references('account_ID')->on('accounts')->cascadeOnDelete();
                $table->foreign('role_ID')->references('role_ID')->on('roles')->cascadeOnDelete();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_roles');
    }
};
