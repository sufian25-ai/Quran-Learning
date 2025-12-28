<?php

namespace App\Services;

use App\Models\User;
use App\Models\GamificationPoint;
use App\Models\LearningStreak;
use App\Models\Badge;
use App\Models\UserBadge;

class GamificationService
{
    // XP values for different actions
    const XP_CLASS_ATTENDANCE = 10;
    const XP_COURSE_COMPLETION = 100;
    const XP_BADGE_EARNED = 25;
    const XP_STREAK_DAILY = 5;
    const XP_FIRST_CLASS = 20;
    const XP_PERFECT_ATTENDANCE = 50;

    /**
     * Award XP points to a user.
     */
    public function awardXP(User $user, int $points, string $sourceType, ?int $sourceId = null, ?string $description = null): GamificationPoint
    {
        $gamificationPoint = GamificationPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'description' => $description,
            'earned_at' => now(),
        ]);

        // Update user's total points
        $user->increment('points', $points);

        return $gamificationPoint;
    }

    /**
     * Award XP for class attendance.
     */
    public function awardClassAttendance(User $user, int $classId): void
    {
        // Check if already awarded for this class
        $exists = GamificationPoint::where('user_id', $user->id)
            ->where('source_type', 'class_attendance')
            ->where('source_id', $classId)
            ->exists();

        if ($exists) {
            return;
        }

        // Award base attendance XP
        $this->awardXP(
            $user,
            self::XP_CLASS_ATTENDANCE,
            'class_attendance',
            $classId,
            'Attended a class'
        );

        // Check for first class badge
        $attendedCount = GamificationPoint::where('user_id', $user->id)
            ->where('source_type', 'class_attendance')
            ->count();

        if ($attendedCount === 1) {
            $this->awardBadge($user, 'first-class');
            $this->awardXP($user, self::XP_FIRST_CLASS, 'badge', null, 'First class attended bonus');
        }

        // Update learning streak
        $this->updateStreak($user);
    }

    /**
     * Award XP for course completion.
     */
    public function awardCourseCompletion(User $user, int $courseId): void
    {
        $this->awardXP(
            $user,
            self::XP_COURSE_COMPLETION,
            'course_completion',
            $courseId,
            'Completed a course'
        );

        // Award course completion badge
        $this->awardBadge($user, 'course-complete');
    }

    /**
     * Update learning streak.
     */
    public function updateStreak(User $user): void
    {
        $streak = LearningStreak::firstOrCreate(
            ['user_id' => $user->id],
            ['current_streak' => 0, 'longest_streak' => 0]
        );

        $today = now()->toDateString();
        $lastActivity = $streak->last_activity_date?->toDateString();

        if ($lastActivity === $today) {
            return; // Already recorded today
        }

        $yesterday = now()->subDay()->toDateString();

        if ($lastActivity === $yesterday) {
            // Continue streak
            $streak->current_streak++;
            $this->awardXP($user, self::XP_STREAK_DAILY, 'streak', null, 'Daily streak bonus');
        } else {
            // Reset streak
            $streak->current_streak = 1;
        }

        if ($streak->current_streak > $streak->longest_streak) {
            $streak->longest_streak = $streak->current_streak;
        }

        $streak->last_activity_date = $today;
        $streak->save();

        // Check for streak milestones
        $this->checkStreakBadges($user, $streak->current_streak);
    }

    /**
     * Award a badge to user.
     */
    public function awardBadge(User $user, string $badgeSlug): bool
    {
        $badge = Badge::where('slug', $badgeSlug)->first();

        if (!$badge) {
            return false;
        }

        // Check if already has badge
        $exists = UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->exists();

        if ($exists) {
            return false;
        }

        UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'earned_at' => now(),
        ]);

        // Award badge XP
        $this->awardXP($user, self::XP_BADGE_EARNED, 'badge_earned', $badge->id, "Earned {$badge->name} badge");

        return true;
    }

    /**
     * Check and award streak milestone badges.
     */
    protected function checkStreakBadges(User $user, int $streakDays): void
    {
        $milestones = [
            7 => 'week-streak',
            30 => 'month-streak',
            100 => 'century-streak',
        ];

        foreach ($milestones as $days => $badgeSlug) {
            if ($streakDays >= $days) {
                $this->awardBadge($user, $badgeSlug);
            }
        }
    }

    /**
     * Get user's gamification summary.
     */
    public function getUserSummary(User $user): array
    {
        $streak = LearningStreak::where('user_id', $user->id)->first();

        return [
            'total_xp' => $user->points ?? 0,
            'current_streak' => $streak?->current_streak ?? 0,
            'longest_streak' => $streak?->longest_streak ?? 0,
            'badges_count' => UserBadge::where('user_id', $user->id)->count(),
            'level' => $this->calculateLevel($user->points ?? 0),
        ];
    }

    /**
     * Calculate user level based on XP.
     */
    public function calculateLevel(int $xp): string
    {
        if ($xp >= 5000)
            return 'Hafiz';
        if ($xp >= 2000)
            return 'Scholar';
        if ($xp >= 1000)
            return 'Advanced';
        if ($xp >= 500)
            return 'Intermediate';
        if ($xp >= 100)
            return 'Beginner';
        return 'Newcomer';
    }
}
