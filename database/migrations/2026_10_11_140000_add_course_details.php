<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->text('overview')->nullable()->after('description');
            $table->text('learning_objectives')->nullable()->after('overview');
        });

        Schema::create('course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('courses')->whereNull('overview')->orderBy('id')->get(['id', 'description'])
            ->each(function ($course) {
                if (! preg_match('#<p>(.*?)</p>#s', (string) $course->description, $match)) {
                    return;
                }
                $intro = trim(html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5));
                if ($intro !== '') {
                    DB::table('courses')->where('id', $course->id)->update(['overview' => $intro]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_resources');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['overview', 'learning_objectives']);
        });
    }
};
