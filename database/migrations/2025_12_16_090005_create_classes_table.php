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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable(); // What will be covered
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('zoom_meeting_id')->nullable();
            $table->string('zoom_join_url')->nullable();
            $table->string('zoom_start_url')->nullable();
            $table->string('zoom_password')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('recording_url')->nullable();
            $table->json('resources')->nullable(); // Attached resources for the class
            $table->text('teacher_notes')->nullable();
            $table->text('homework')->nullable();
            $table->integer('attendee_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id', 'scheduled_at']);
            $table->index(['enrollment_id', 'scheduled_at']);
            $table->index(['teacher_id', 'scheduled_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
