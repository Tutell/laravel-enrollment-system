<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            if (! Schema::hasColumn('students', 'archived_at')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->timestamp('archived_at')->nullable()->after('status');
                });
            }
            if (! Schema::hasColumn('students', 'archive_reason')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('archive_reason', 255)->nullable()->after('archived_at');
                });
            }
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `students` MODIFY `status` ENUM('active','inactive','pending','archived') NOT NULL DEFAULT 'pending'");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE students ADD VALUE IF NOT EXISTS 'archived' FOR TYPE status");
                // If status is not a native enum type, fall back to varchar change
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `students` MODIFY `status` ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending'");
            }
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'archived_at')) {
                    $table->dropColumn('archived_at');
                }
                if (Schema::hasColumn('students', 'archive_reason')) {
                    $table->dropColumn('archive_reason');
                }
            });
        }
    }
};
