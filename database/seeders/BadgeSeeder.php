<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Attendance Badges
            [
                'code' => 'first_class',
                'name' => 'First Steps',
                'description' => 'Attended your first class',
                'icon' => 'trophy',
                'category' => 'attendance',
                'points_value' => 10,
                'criteria' => ['type' => 'classes_attended', 'count' => 1],
            ],
            [
                'code' => 'consistent_learner',
                'name' => 'Consistent Learner',
                'description' => 'Attended 10 classes',
                'icon' => 'calendar-check',
                'category' => 'attendance',
                'points_value' => 50,
                'criteria' => ['type' => 'classes_attended', 'count' => 10],
            ],
            [
                'code' => 'dedicated_student',
                'name' => 'Dedicated Student',
                'description' => 'Attended 50 classes',
                'icon' => 'star',
                'category' => 'attendance',
                'points_value' => 200,
                'criteria' => ['type' => 'classes_attended', 'count' => 50],
            ],

            // Streak Badges
            [
                'code' => 'week_streak',
                'name' => 'Week Warrior',
                'description' => 'Maintained a 7-day learning streak',
                'icon' => 'fire',
                'category' => 'streak',
                'points_value' => 70,
                'criteria' => ['type' => 'streak_days', 'count' => 7],
            ],
            [
                'code' => 'month_streak',
                'name' => 'Monthly Master',
                'description' => 'Maintained a 30-day learning streak',
                'icon' => 'flame',
                'category' => 'streak',
                'points_value' => 300,
                'criteria' => ['type' => 'streak_days', 'count' => 30],
            ],
            [
                'code' => 'century_streak',
                'name' => 'Century Champion',
                'description' => 'Maintained a 100-day learning streak',
                'icon' => 'crown',
                'category' => 'streak',
                'points_value' => 1000,
                'criteria' => ['type' => 'streak_days', 'count' => 100],
            ],

            // Progress Badges
            [
                'code' => 'course_completed',
                'name' => 'Course Graduate',
                'description' => 'Completed your first course',
                'icon' => 'graduation-cap',
                'category' => 'progress',
                'points_value' => 500,
                'criteria' => ['type' => 'courses_completed', 'count' => 1],
            ],
            [
                'code' => 'tajweed_master',
                'name' => 'Tajweed Master',
                'description' => 'Completed a Tajweed course with 90%+ score',
                'icon' => 'book-open',
                'category' => 'progress',
                'points_value' => 750,
                'criteria' => ['type' => 'tajweed_score', 'min_score' => 90],
            ],
            [
                'code' => 'quran_reader',
                'name' => 'Quran Reader',
                'description' => 'Can read the Quran independently',
                'icon' => 'book',
                'category' => 'progress',
                'points_value' => 1000,
                'criteria' => ['type' => 'reading_level', 'level' => 'independent'],
            ],

            // Social Badges
            [
                'code' => 'first_review',
                'name' => 'Feedback Provider',
                'description' => 'Left your first course review',
                'icon' => 'message-square',
                'category' => 'social',
                'points_value' => 25,
                'criteria' => ['type' => 'reviews_count', 'count' => 1],
            ],
            [
                'code' => 'referral_champion',
                'name' => 'Referral Champion',
                'description' => 'Referred 5 students who enrolled',
                'icon' => 'users',
                'category' => 'social',
                'points_value' => 250,
                'criteria' => ['type' => 'referrals', 'count' => 5],
            ],

            // Special Badges
            [
                'code' => 'ramadan_learner',
                'name' => 'Ramadan Learner',
                'description' => 'Studied during the blessed month of Ramadan',
                'icon' => 'moon',
                'category' => 'special',
                'points_value' => 100,
                'criteria' => ['type' => 'special_event', 'event' => 'ramadan'],
            ],
            [
                'code' => 'perfect_attendance',
                'name' => 'Perfect Attendance',
                'description' => 'Never missed a class in a course',
                'icon' => 'check-circle',
                'category' => 'special',
                'points_value' => 500,
                'criteria' => ['type' => 'perfect_attendance', 'course' => true],
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::create($badgeData);
        }
    }
}
