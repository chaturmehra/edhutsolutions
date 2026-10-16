<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('includes_label')->nullable();
            $table->text('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->text('packages_note')->nullable()->after('is_featured');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('course_package_id')->nullable()->after('course_id')->constrained()->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('course_package_id')->nullable()->after('course_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_package_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_package_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('packages_note');
        });

        Schema::dropIfExists('course_packages');
    }
};
