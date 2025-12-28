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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('milestone_type'); // class_attended, homework_completed, quiz_passed, page_memorized, module_completed
            $table->unsignedBigInteger('milestone_id')->nullable(); // Reference to specific class, quiz, etc.
            $table->integer('score')->nullable(); // Score if applicable
            $table->integer('points_earned')->default(0);
            $table->json('metadata')->nullable(); // Additional details
            $table->timestamp('achieved_at');
            $table->timestamps();

            $table->index(['enrollment_id', 'milestone_type']);
            $table->index(['student_id', 'achieved_at']);
        });

        // Badges/Achievements table
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('category')->default('general'); // attendance, progress, social, special
            $table->json('criteria')->nullable(); // Conditions to earn this badge
            $table->integer('points_value')->default(0);
            $table->timestamps();
        });

        // User badges (earned badges)
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']);
        });

        // Learning streaks
        Schema::create('learning_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_streaks');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('student_progress');
    }
};
