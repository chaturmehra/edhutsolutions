<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->foreignId('service_plan_id')->nullable()->after('course_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('service_plan_id')->nullable()->after('course_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_plan_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_plan_id');
        });
    }
};
