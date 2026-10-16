<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('credentials')->nullable();
            $table->string('experience', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_trainer', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained()->cascadeOnDelete();
            $table->primary(['course_id', 'trainer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_trainer');
        Schema::dropIfExists('trainers');
    }
};
