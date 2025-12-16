<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE `accounts` MODIFY `Email` VARCHAR(255) NULL');
        } catch (\Throwable $e) {
            // ignore if already nullable
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `accounts` MODIFY `Email` VARCHAR(255) NOT NULL');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
