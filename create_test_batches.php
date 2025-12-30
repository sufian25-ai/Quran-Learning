<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;

// Get first course
$course = Course::first();

if (!$course) {
    echo "No courses found! Create a course first.\n";
    exit;
}

// Get teachers
$teachers = User::whereHas('roles', function ($q) {
    $q->where('name', 'teacher');
})->get();

if ($teachers->isEmpty()) {
    echo "No teachers found! Create teachers first.\n";
    exit;
}

// Create batches
$batches = [];

// Private Batches
for ($i = 1; $i <= 3; $i++) {
    $batch = Batch::create([
        'course_id' => $course->id,
        'teacher_id' => $teachers->random()->id,
        'name' => "Private Batch $i",
        'type' => 'private',
        'max_students' => 1,
        'description' => "One-on-one private Quran learning session",
        'status' => 'active',
        'start_date' => now(),
    ]);
    $batches[] = $batch;
    echo "Created: {$batch->name} (Teacher: {$batch->teacher->name})\n";
}

// Group Batches
for ($i = 1; $i <= 2; $i++) {
    $batch = Batch::create([
        'course_id' => $course->id,
        'teacher_id' => $teachers->random()->id,
        'name' => "Group Batch $i",
        'type' => 'group',
        'max_students' => 10,
        'description' => "Group Quran learning class",
        'status' => 'active',
        'start_date' => now(),
    ]);
    $batches[] = $batch;
    echo "Created: {$batch->name} (Teacher: {$batch->teacher->name})\n";
}

echo "\nTotal batches created: " . count($batches) . "\n";
echo "Now refresh the assignment page!\n";
