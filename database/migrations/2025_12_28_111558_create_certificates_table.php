<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained()->cascadeOnDelete();

            // Certificate details
            $table->string('certificate_number')->unique(); // e.g., QLC-2024-00001
            $table->string('student_name');
            $table->string('course_title');
            $table->text('course_description')->nullable();

            // Completion details
            $table->decimal('completion_percentage', 5, 2)->default(100.00);
            $table->decimal('grade', 5, 2)->nullable(); // Score/Grade if applicable
            $table->date('course_started_at')->nullable();
            $table->date('course_completed_at');

            // Certificate metadata
            $table->string('pdf_path')->nullable(); // Storage path to PDF
            $table->string('template', 50)->default('default'); // Certificate template used
            $table->boolean('is_verified')->default(true);

            // Verification
            $table->string('verification_code', 12)->unique(); // Short code for quick verify
            $table->string('qr_code_path')->nullable(); // QR code image path

            // Instructor/Authority
            $table->string('issued_by')->nullable(); // Name of issuing authority
            $table->string('instructor_name')->nullable();
            $table->string('instructor_signature')->nullable(); // Signature image path

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'course_id']);
            $table->index('certificate_number');
            $table->index('verification_code');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
