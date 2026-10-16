<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('kind', 20)->default('practice')->after('description')->index();
            $table->unsignedInteger('questions_per_attempt')->nullable()->after('time_limit_minutes');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->json('question_ids')->nullable()->after('user_id');
        });

        Schema::table('course_pdfs', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('course_pdfs', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('question_ids');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['kind']);
            $table->dropColumn(['kind', 'questions_per_attempt']);
        });
    }
};
