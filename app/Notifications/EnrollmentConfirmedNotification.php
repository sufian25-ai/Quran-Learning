<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Enrollment;

class EnrollmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Enrollment $enrollment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->enrollment->course?->title ?? 'your course';
        $batchName = $this->enrollment->batch?->name;
        $startDate = $this->enrollment->batch?->start_date?->format('F j, Y');

        $mail = (new MailMessage)
            ->subject("Enrollment Confirmed: {$courseName}")
            ->greeting("Assalamu Alaikum, {$notifiable->name}!")
            ->line("Alhamdulillah! Your enrollment in **{$courseName}** has been confirmed.")
            ->line("We're excited to have you on this blessed journey of learning.");

        if ($batchName && $startDate) {
            $mail->line("**Batch:** {$batchName}")
                ->line("**Start Date:** {$startDate}");
        }

        return $mail
            ->action('View My Courses', url('/enrollments'))
            ->line('May Allah bless your learning journey!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'enrollment_confirmed',
            'title' => 'Enrollment Confirmed',
            'message' => "You're enrolled in {$this->enrollment->course?->title}",
            'action_url' => '/enrollments',
            'action_text' => 'View Course',
            'enrollment_id' => $this->enrollment->id,
        ];
    }
}
