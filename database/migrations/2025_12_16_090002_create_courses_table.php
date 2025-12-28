<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('syllabus')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('video_preview')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('beginner');
            $table->string('category')->default('quran_reading'); // quran_reading, tajweed, hifz, arabic, islamic_studies
            $table->json('languages')->nullable(); // Course available in these languages
            $table->integer('duration_weeks')->default(12);
            $table->integer('classes_per_week')->default(3);
            $table->integer('class_duration_minutes')->default(60);
            $table->decimal('price_group', 10, 2)->default(49.00);
            $table->decimal('price_private', 10, 2)->default(99.00);
            $table->integer('max_students_per_batch')->default(10);
            $table->json('requirements')->nullable(); // Prerequisites
            $table->json('learning_outcomes')->nullable(); // What students will learn
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('popularity_score')->default(0);
            $table->integer('total_enrollments')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'is_featured']);
            $table->index('category');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
