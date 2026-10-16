<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->text('scenario')->nullable()->after('prompt');
        });

        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            $table->text('answer_text')->nullable()->after('selected_option_ids');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn('scenario');
        });

        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            $table->dropColumn('answer_text');
        });
    }
};
