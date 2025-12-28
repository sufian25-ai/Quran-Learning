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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // e.g., "January 2025 - Morning Batch"
            $table->text('description')->nullable();
            $table->integer('max_students')->default(10);
            $table->integer('enrolled_students')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('schedule'); // [{"day": "monday", "time": "09:00", "timezone": "UTC"}, ...]
            $table->enum('status', ['draft', 'upcoming', 'active', 'completed', 'cancelled'])->default('draft');
            $table->decimal('price_override', 10, 2)->nullable(); // Override course price for this batch
            $table->boolean('is_accepting_enrollments')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id', 'status']);
            $table->index(['teacher_id', 'status']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
