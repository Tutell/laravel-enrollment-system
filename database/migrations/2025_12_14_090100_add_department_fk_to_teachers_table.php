<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (! Schema::hasColumn('teachers', 'department_ID')) {
                    $table->unsignedBigInteger('department_ID')->nullable()->after('account_ID');
                    $table->foreign('department_ID')->references('department_ID')->on('departments')->nullOnDelete();
                    $table->index('department_ID');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (Schema::hasColumn('teachers', 'department_ID')) {
                    $table->dropForeign(['department_ID']);
                    $table->dropIndex(['department_ID']);
                    $table->dropColumn('department_ID');
                }
            });
        }
    }
};
