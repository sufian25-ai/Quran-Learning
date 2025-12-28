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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // For guest tracking
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // If logged in
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'pending', 'closed'])->default('pending');
            $table->enum('department', ['general', 'teacher', 'admin', 'support'])->default('general');
            $table->text('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_guest_online')->default(true);
            $table->timestamps();

            $table->index(['status', 'department']);
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
