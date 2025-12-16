<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id('payment_ID');

                $table->unsignedBigInteger('account_ID')->nullable();
                $table->unsignedBigInteger('student_ID')->nullable();
                $table->unsignedBigInteger('enrollment_ID')->nullable();

                $table->string('provider', 20);
                $table->integer('amount');
                $table->string('currency', 3)->default('PHP');
                $table->string('status', 30)->default('pending');
                $table->string('reference', 100)->nullable();
                $table->string('checkout_url', 255)->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
