<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Notifications\ClassReminderNotification;

class SendClassReminders extends Command
{
    protected $signature = 'reminders:classes {--minutes=30 : Minutes before class to send reminder}';
    protected $description = 'Send reminders for upcoming classes';

    public function handle(): int
    {
        $minutes = $this->option('minutes');
        $startWindow = now()->addMinutes($minutes - 5);
        $endWindow = now()->addMinutes($minutes + 5);

        $upcomingClasses = ClassSession::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$startWindow, $endWindow])
            ->with(['batch.enrollments.user'])
            ->get();

        $notificationsSent = 0;

        foreach ($upcomingClasses as $class) {
            // Get students enrolled in this batch
            if ($class->batch_id) {
                $enrollments = Enrollment::where('batch_id', $class->batch_id)
                    ->where('status', 'active')
                    ->with('user')
                    ->get();

                foreach ($enrollments as $enrollment) {
                    if ($enrollment->user) {
                        $enrollment->user->notify(new ClassReminderNotification($class, $minutes));
                        $notificationsSent++;
                    }
                }
            }

            // For private classes
            if ($class->enrollment_id) {
                $enrollment = Enrollment::with('user')->find($class->enrollment_id);
                if ($enrollment?->user) {
                    $enrollment->user->notify(new ClassReminderNotification($class, $minutes));
                    $notificationsSent++;
                }
            }

            // Notify the teacher too
            if ($class->teacher) {
                $class->teacher->notify(new ClassReminderNotification($class, $minutes));
                $notificationsSent++;
            }
        }

        $this->info("Sent {$notificationsSent} class reminders for {$upcomingClasses->count()} classes.");

        return Command::SUCCESS;
    }
}
