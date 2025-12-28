<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Badge;
use App\Models\Course;
use App\Models\TeacherProfile;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BadgeSeeder::class,
        ]);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@quranlearning.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Sample Teacher
        $teacher = User::create([
            'name' => 'Sheikh Ahmed',
            'email' => 'teacher@quranlearning.com',
            'password' => bcrypt('password'),
            'phone' => '+8801700000000',
            'timezone' => 'Asia/Dhaka',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $teacher->assignRole('teacher');

        // Create Teacher Profile
        TeacherProfile::create([
            'user_id' => $teacher->id,
            'bio' => 'Experienced Quran teacher with 10+ years of teaching Tajweed and Hifz.',
            'qualifications' => 'Bachelor in Islamic Studies from International Islamic University',
            'certifications' => ['Ijazah in Quran Recitation', 'Teaching Certificate'],
            'specializations' => ['Tajweed', 'Hifz', 'Arabic Language'],
            'languages_spoken' => ['Arabic', 'English', 'Bengali'],
            'hourly_rate' => 30.00,
            'rating' => 4.8,
            'is_verified' => true,
            'verified_at' => now(),
            'availability' => [
                ['day' => 'monday', 'slots' => ['09:00-12:00', '14:00-18:00']],
                ['day' => 'tuesday', 'slots' => ['09:00-12:00', '14:00-18:00']],
                ['day' => 'wednesday', 'slots' => ['09:00-12:00', '14:00-18:00']],
                ['day' => 'thursday', 'slots' => ['09:00-12:00', '14:00-18:00']],
                ['day' => 'friday', 'slots' => ['09:00-12:00']],
                ['day' => 'saturday', 'slots' => ['10:00-15:00']],
            ],
        ]);

        // Create Sample Student
        $student = User::create([
            'name' => 'Test Student',
            'email' => 'student@quranlearning.com',
            'password' => bcrypt('password'),
            'phone' => '+8801800000000',
            'timezone' => 'Asia/Dhaka',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $student->assignRole('student');

        // Create Sample Courses
        $this->createSampleCourses();
    }

    private function createSampleCourses(): void
    {
        $courses = [
            [
                'title' => 'Quran Reading Basics',
                'slug' => 'quran-reading-basics',
                'short_description' => 'Learn to read the Quran with proper pronunciation',
                'description' => 'This comprehensive course will take you from zero to reading the Quran fluently. Perfect for beginners with no prior Arabic knowledge.',
                'level' => 'beginner',
                'category' => 'quran_reading',
                'duration_weeks' => 12,
                'classes_per_week' => 3,
                'class_duration_minutes' => 60,
                'price_group' => 49.00,
                'price_private' => 99.00,
                'max_students_per_batch' => 10,
                'is_published' => true,
                'is_featured' => true,
                'learning_outcomes' => [
                    'Read Arabic letters correctly',
                    'Understand basic Tajweed rules',
                    'Read short Surahs independently',
                    'Develop consistent reading practice',
                ],
            ],
            [
                'title' => 'Tajweed Mastery',
                'slug' => 'tajweed-mastery',
                'short_description' => 'Master the rules of Quranic recitation',
                'description' => 'Dive deep into the science of Tajweed. Learn all the rules of proper Quranic recitation from qualified teachers.',
                'level' => 'intermediate',
                'category' => 'tajweed',
                'duration_weeks' => 16,
                'classes_per_week' => 3,
                'class_duration_minutes' => 60,
                'price_group' => 69.00,
                'price_private' => 129.00,
                'max_students_per_batch' => 8,
                'is_published' => true,
                'is_featured' => true,
                'learning_outcomes' => [
                    'Master all Tajweed rules',
                    'Perfect your pronunciation of letters',
                    'Apply rules while reading any Quran verse',
                    'Identify and correct common mistakes',
                ],
            ],
            [
                'title' => 'Quran Memorization (Hifz)',
                'slug' => 'quran-memorization-hifz',
                'short_description' => 'Structured program to memorize the Holy Quran',
                'description' => 'A comprehensive Hifz program with personalized attention. Memorize the Quran with proper techniques and revision schedules.',
                'level' => 'all_levels',
                'category' => 'hifz',
                'duration_weeks' => 52,
                'classes_per_week' => 5,
                'class_duration_minutes' => 60,
                'price_group' => 99.00,
                'price_private' => 199.00,
                'max_students_per_batch' => 5,
                'is_published' => true,
                'is_featured' => true,
                'learning_outcomes' => [
                    'Develop effective memorization techniques',
                    'Create a sustainable revision schedule',
                    'Memorize with correct Tajweed',
                    'Build strong Quran retention',
                ],
            ],
            [
                'title' => 'Arabic for Quran Understanding',
                'slug' => 'arabic-for-quran-understanding',
                'short_description' => 'Learn Arabic to understand the Quran directly',
                'description' => 'This course teaches you Quranic Arabic so you can understand the meaning of what you recite directly without translation.',
                'level' => 'beginner',
                'category' => 'arabic',
                'duration_weeks' => 20,
                'classes_per_week' => 3,
                'class_duration_minutes' => 60,
                'price_group' => 79.00,
                'price_private' => 149.00,
                'max_students_per_batch' => 10,
                'is_published' => true,
                'is_featured' => false,
                'learning_outcomes' => [
                    'Understand 80% of Quranic vocabulary',
                    'Parse basic Arabic grammar',
                    'Read Quran with understanding',
                    'Connect with the message directly',
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}
