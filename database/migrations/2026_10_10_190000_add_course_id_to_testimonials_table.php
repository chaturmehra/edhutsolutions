<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        $courses = DB::table('courses')->pluck('id', 'slug');
        $keywords = [
            'pgmp' => 'pgmp-certification-training',
            'pfmp' => 'pfmp-certification-training',
            'rmp' => 'pmi-rmp-certification-training',
            'pmp' => 'pmp-certification-training',
        ];

        DB::table('testimonials')->whereNull('course_id')->orderBy('id')->get(['id', 'headline', 'quote'])
            ->each(function ($testimonial) use ($courses, $keywords) {
                $text = strtolower($testimonial->headline.' '.$testimonial->quote);
                foreach ($keywords as $keyword => $slug) {
                    if (str_contains($text, $keyword) && isset($courses[$slug])) {
                        DB::table('testimonials')->where('id', $testimonial->id)->update(['course_id' => $courses[$slug]]);

                        return;
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_id');
        });
    }
};
