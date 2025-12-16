<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'grade_level')) {
                $table->unsignedTinyInteger('grade_level')->nullable()->after('name');
                $table->index('grade_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'grade_level')) {
                $table->dropIndex(['grade_level']);
                $table->dropColumn('grade_level');
            }
        });
    }
};
