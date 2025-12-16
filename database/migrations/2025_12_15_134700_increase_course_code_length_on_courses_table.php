<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `courses` MODIFY `course_code` VARCHAR(64) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE courses ALTER COLUMN course_code TYPE VARCHAR(64)');
        } elseif ($driver === 'sqlite') {
            // SQLite cannot alter column types easily; recreate table if needed.
            // As a no-op fallback, attempt to add a check by rebuilding only if necessary.
            // Most environments here use MySQL per error context.
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `courses` MODIFY `course_code` VARCHAR(20) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE courses ALTER COLUMN course_code TYPE VARCHAR(20)');
        } elseif ($driver === 'sqlite') {
            // No-op for SQLite as above.
        }
    }
};

