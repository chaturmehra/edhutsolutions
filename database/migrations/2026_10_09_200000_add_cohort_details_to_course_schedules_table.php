<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->string('delivery_mode')->nullable()->after('hours_label');
            $table->json('session_dates')->nullable()->after('delivery_mode');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('course_schedule_id')->nullable()->after('course_package_id')->constrained()->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('course_schedule_id')->nullable()->after('course_package_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_schedule_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_schedule_id');
        });

        Schema::table('course_schedules', function (Blueprint $table) {
            $table->dropColumn(['delivery_mode', 'session_dates']);
        });
    }
};
