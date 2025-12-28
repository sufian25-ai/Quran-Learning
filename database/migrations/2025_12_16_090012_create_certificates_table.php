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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('student_name'); // Name as it appears on certificate
            $table->string('course_name'); // Course name at time of completion
            $table->date('completion_date');
            $table->date('issued_at');
            $table->date('expires_at')->nullable(); // For certifications that expire
            $table->string('pdf_path')->nullable();
            $table->string('verification_url')->nullable();
            $table->json('metadata')->nullable(); // Additional certificate data
            $table->boolean('is_valid')->default(true);
            $table->timestamps();

            $table->index('student_id');
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
