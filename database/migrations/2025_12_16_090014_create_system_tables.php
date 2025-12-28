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
        // Settings table for system configuration
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, json, integer
            $table->string('group')->default('general'); // general, payment, email, sms, zoom
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Announcements for platform-wide notices
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->enum('type', ['info', 'warning', 'success', 'error'])->default('info');
            $table->enum('audience', ['all', 'students', 'teachers', 'admins'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissible')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'audience']);
        });

        // Activity log for audit trail
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->default('default');
            $table->text('description');
            $table->nullableMorphs('subject'); // The model that was changed
            $table->nullableMorphs('causer'); // Who made the change
            $table->json('properties')->nullable(); // Old/new values
            $table->string('event')->nullable(); // created, updated, deleted
            $table->timestamp('created_at')->nullable();

            $table->index('log_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('settings');
    }
};
