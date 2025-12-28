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
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->text('qualifications')->nullable();
            $table->json('certifications')->nullable(); // Ijazah, teaching certs, etc.
            $table->json('specializations')->nullable(); // Tajweed, Hifz, Tafsir, etc.
            $table->json('languages_spoken')->nullable(); // Languages the teacher speaks
            $table->decimal('hourly_rate', 8, 2)->default(25.00);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_students')->default(0);
            $table->integer('total_classes')->default(0);
            $table->json('availability')->nullable(); // Weekly availability schedule
            $table->string('video_intro_url')->nullable(); // Teacher introduction video
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_accepting_students')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
