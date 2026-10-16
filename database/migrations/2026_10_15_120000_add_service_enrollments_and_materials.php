<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('active')->index();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'service_id']);
        });

        foreach (['course_pdfs', 'course_videos'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->unsignedBigInteger('course_id')->nullable()->change();
                $table->foreignId('service_id')->nullable()->after('course_id')->constrained()->cascadeOnDelete();
            });
        }

        $approved = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('service_plans', 'service_plans.id', '=', 'order_items.service_plan_id')
            ->where('orders.status', 'approved')
            ->whereNull('orders.deleted_at')
            ->orderBy('orders.approved_at')
            ->get(['orders.user_id', 'orders.id as order_id', 'orders.approved_at', 'service_plans.id as plan_id', 'service_plans.service_id']);

        foreach ($approved as $row) {
            DB::table('service_enrollments')->updateOrInsert(
                ['user_id' => $row->user_id, 'service_id' => $row->service_id],
                [
                    'service_plan_id' => $row->plan_id,
                    'order_id' => $row->order_id,
                    'status' => 'active',
                    'enrolled_at' => $row->approved_at ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        foreach (['course_pdfs', 'course_videos'] as $name) {
            DB::table($name)->whereNull('course_id')->delete();
            Schema::table($name, function (Blueprint $table) {
                $table->dropConstrainedForeignId('service_id');
                $table->unsignedBigInteger('course_id')->nullable(false)->change();
            });
        }

        Schema::dropIfExists('service_enrollments');
    }
};
