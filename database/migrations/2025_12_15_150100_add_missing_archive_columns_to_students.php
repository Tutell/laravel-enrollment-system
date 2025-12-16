<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('students', 'archive_reason')) {
                $table->string('archive_reason', 255)->nullable()->after('archived_at');
            }
        });
        try {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `students` MODIFY `status` ENUM('active','inactive','pending','archived') NOT NULL DEFAULT 'pending'");
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'archive_reason')) {
                $table->dropColumn('archive_reason');
            }
            if (Schema::hasColumn('students', 'archived_at')) {
                $table->dropColumn('archived_at');
            }
        });
        try {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `students` MODIFY `status` ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending'");
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
};

