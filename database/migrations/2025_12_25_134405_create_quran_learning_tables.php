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
        // Quran Surahs (114 chapters)
        Schema::create('quran_surahs', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number')->unique();
            $table->string('name_arabic');
            $table->string('name_english');
            $table->string('name_transliteration');
            $table->string('name_bangla')->nullable();
            $table->integer('total_ayahs');
            $table->enum('revelation_type', ['meccan', 'medinan']);
            $table->integer('revelation_order');
            $table->integer('juz_start')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Quran Ayahs (verses) - will be populated via API
        Schema::create('quran_ayahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surah_id')->constrained('quran_surahs')->onDelete('cascade');
            $table->integer('ayah_number');
            $table->integer('ayah_key'); // Global ayah number (1-6236)
            $table->text('text_arabic');
            $table->text('text_uthmani')->nullable();
            $table->text('translation_english')->nullable();
            $table->text('translation_bangla')->nullable();
            $table->text('transliteration')->nullable();
            $table->string('audio_url')->nullable();
            $table->integer('juz_number')->nullable();
            $table->integer('hizb_number')->nullable();
            $table->integer('page_number')->nullable();
            $table->timestamps();

            $table->unique(['surah_id', 'ayah_number']);
        });

        // Tajweed Rules
        Schema::create('tajweed_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name_arabic');
            $table->string('name_english');
            $table->string('name_bangla')->nullable();
            $table->text('description');
            $table->text('example')->nullable();
            $table->string('color_code')->nullable(); // For highlighting
            $table->integer('difficulty_level')->default(1); // 1-5
            $table->timestamps();
        });

        // Student Surah Progress
        Schema::create('student_surah_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('surah_id')->constrained('quran_surahs')->onDelete('cascade');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'memorized'])->default('not_started');
            $table->integer('last_ayah_read')->default(0);
            $table->integer('ayahs_memorized')->default(0);
            $table->integer('read_count')->default(0);
            $table->integer('listen_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'surah_id']);
        });

        // Student Ayah Progress (detailed tracking)
        Schema::create('student_ayah_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ayah_id')->constrained('quran_ayahs')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_memorized')->default(false);
            $table->integer('recitation_count')->default(0);
            $table->integer('mistake_count')->default(0);
            $table->decimal('confidence_score', 3, 2)->default(0); // 0.00 - 1.00
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'ayah_id']);
        });

        // Student Tajweed Skills
        Schema::create('student_tajweed_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tajweed_rule_id')->constrained('tajweed_rules')->onDelete('cascade');
            $table->integer('skill_level')->default(0); // 0-100
            $table->integer('practice_count')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('mistake_count')->default(0);
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tajweed_rule_id']);
        });

        // Recitation Submissions
        Schema::create('recitation_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('surah_id')->constrained('quran_surahs')->onDelete('cascade');
            $table->integer('ayah_from');
            $table->integer('ayah_to');
            $table->string('audio_path');
            $table->integer('duration_seconds')->nullable();
            $table->enum('status', ['pending', 'in_review', 'reviewed', 'approved', 'needs_improvement'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('student_notes')->nullable();
            $table->timestamps();
        });

        // Teacher Feedback on Recitations
        Schema::create('recitation_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('recitation_submissions')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->integer('overall_rating')->default(0); // 1-5 stars
            $table->integer('pronunciation_score')->default(0); // 0-100
            $table->integer('tajweed_score')->default(0); // 0-100
            $table->integer('fluency_score')->default(0); // 0-100
            $table->text('feedback_text')->nullable();
            $table->json('mistakes')->nullable(); // Array of mistake objects
            $table->json('improvements')->nullable(); // Suggested improvements
            $table->string('audio_feedback_path')->nullable(); // Teacher's audio feedback
            $table->timestamps();
        });

        // Memorization Goals
        Schema::create('memorization_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('goal_type', ['surah', 'juz', 'custom']);
            $table->json('target_surahs')->nullable(); // Array of surah IDs
            $table->integer('target_juz')->nullable();
            $table->integer('target_ayahs')->nullable();
            $table->date('target_date')->nullable();
            $table->integer('daily_target_ayahs')->default(5);
            $table->enum('status', ['active', 'completed', 'paused', 'abandoned'])->default('active');
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
        });

        // Daily Learning Sessions
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('session_date');
            $table->integer('ayahs_read')->default(0);
            $table->integer('ayahs_memorized')->default(0);
            $table->integer('ayahs_revised')->default(0);
            $table->integer('recitations_submitted')->default(0);
            $table->integer('time_spent_minutes')->default(0);
            $table->json('surahs_practiced')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_sessions');
        Schema::dropIfExists('memorization_goals');
        Schema::dropIfExists('recitation_feedback');
        Schema::dropIfExists('recitation_submissions');
        Schema::dropIfExists('student_tajweed_skills');
        Schema::dropIfExists('student_ayah_progress');
        Schema::dropIfExists('student_surah_progress');
        Schema::dropIfExists('tajweed_rules');
        Schema::dropIfExists('quran_ayahs');
        Schema::dropIfExists('quran_surahs');
    }
};
