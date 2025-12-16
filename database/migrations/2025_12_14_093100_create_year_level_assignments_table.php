<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('year_level_assignments')) {
            Schema::create('year_level_assignments', function (Blueprint $table) {
                $table->id('assignment_ID');
                $table->unsignedBigInteger('year_level_ID');
                $table->unsignedBigInteger('teacher_ID');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamp('requested_at')->nullable();
                $table->unsignedBigInteger('approved_by_account_ID')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('year_level_ID')->references('year_level_ID')->on('year_levels')->cascadeOnDelete();
                $table->foreign('teacher_ID')->references('teacher_ID')->on('teachers')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('year_level_assignments');
    }
};
