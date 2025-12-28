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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('enrollment_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->json('ratings_breakdown')->nullable(); // Teaching quality, content, support, etc.
            $table->boolean('is_verified')->default(false); // Verified purchase
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->text('admin_response')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'student_id']); // One review per course per student
            $table->index('teacher_id');
            $table->index(['is_visible', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
