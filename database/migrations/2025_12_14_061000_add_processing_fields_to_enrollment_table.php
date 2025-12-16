<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->unsignedBigInteger('processed_by_account_ID')->nullable()->after('status');
            $table->timestamp('processed_at')->nullable()->after('processed_by_account_ID');
        });
    }

    public function down(): void
    {
        Schema::table('enrollment', function (Blueprint $table) {
            $table->dropColumn(['processed_by_account_ID', 'processed_at']);
        });
    }
};
