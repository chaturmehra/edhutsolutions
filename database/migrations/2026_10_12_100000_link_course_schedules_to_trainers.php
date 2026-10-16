<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->foreignId('trainer_id')->nullable()->after('delivery_mode')->constrained()->nullOnDelete();
        });

        $now = now();
        $names = DB::table('course_schedules')->whereNotNull('trainer')->pluck('trainer', 'id');
        foreach ($names as $id => $name) {
            $name = trim((string) $name);
            if ($name === '' || strcasecmp($name, 'PMI Authorized Trainer') === 0) {
                continue;
            }

            $trainerId = DB::table('trainers')->whereNull('deleted_at')->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->value('id')
                ?? DB::table('trainers')->insertGetId(['name' => $name, 'sort_order' => 0, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);

            DB::table('course_schedules')->where('id', $id)->update(['trainer_id' => $trainerId]);
        }

        Schema::table('course_schedules', function (Blueprint $table) {
            $table->dropColumn('trainer');
        });
    }

    public function down(): void
    {
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->string('trainer')->nullable()->after('delivery_mode');
        });

        foreach (DB::table('course_schedules')->whereNotNull('trainer_id')->pluck('trainer_id', 'id') as $id => $trainerId) {
            DB::table('course_schedules')->where('id', $id)->update([
                'trainer' => DB::table('trainers')->where('id', $trainerId)->value('name'),
            ]);
        }

        Schema::table('course_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trainer_id');
        });
    }
};
