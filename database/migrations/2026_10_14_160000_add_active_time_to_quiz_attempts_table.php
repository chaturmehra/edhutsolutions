<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->unsignedInteger('elapsed_seconds')->nullable()->after('started_at');
            $table->timestamp('last_seen_at')->nullable()->after('elapsed_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['elapsed_seconds', 'last_seen_at']);
        });
    }
};
