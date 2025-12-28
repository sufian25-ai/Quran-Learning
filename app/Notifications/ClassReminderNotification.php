<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ClassSession;

class ClassReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ClassSession $classSession,
        protected int $minutesBefore = 30
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $startTime = $this->classSession->scheduled_at->format('g:i A');
        $date = $this->classSession->scheduled_at->format('l, F j, Y');

        return (new MailMessage)
            ->subject("Class Reminder: {$this->classSession->title}")
            ->greeting("Assalamu Alaikum, {$notifiable->name}!")
            ->line("Your class **{$this->classSession->title}** is starting in {$this->minutesBefore} minutes.")
            ->line("**Date:** {$date}")
            ->line("**Time:** {$startTime}")
            ->line("**Duration:** {$this->classSession->duration_minutes} minutes")
            ->action('Join Class', $this->classSession->zoom_join_url ?? url('/classes'))
            ->line('May your learning be blessed!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'class_reminder',
            'title' => 'Class Starting Soon',
            'message' => "Your class '{$this->classSession->title}' starts in {$this->minutesBefore} minutes",
            'action_url' => "/classes/{$this->classSession->id}",
            'action_text' => 'Join Class',
            'class_session_id' => $this->classSession->id,
        ];
    }
}
