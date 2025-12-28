<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Badge;

class BadgeEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Badge $badge,
        protected int $xpAwarded = 25
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🏅 Badge Earned: {$this->badge->name}")
            ->greeting("Congratulations, {$notifiable->name}!")
            ->line("You've earned the **{$this->badge->name}** badge! {$this->badge->icon}")
            ->line($this->badge->description)
            ->line("You also earned **{$this->xpAwarded} XP** for this achievement!")
            ->action('View Your Achievements', url('/leaderboard'))
            ->line('Keep up the great work!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'badge_earned',
            'title' => "Badge Earned: {$this->badge->name}",
            'message' => "You've earned the {$this->badge->name} badge! +{$this->xpAwarded} XP",
            'action_url' => '/leaderboard',
            'action_text' => 'View Achievements',
            'badge_id' => $this->badge->id,
        ];
    }
}
