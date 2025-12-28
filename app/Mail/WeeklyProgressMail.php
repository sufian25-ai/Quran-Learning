<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyProgressMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $stats)
    {
        $this->user = $user;
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Your Weekly Quran Learning Progress Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-progress',
            with: [
                'userName' => $this->user->name,
                'ayahsRead' => $this->stats['ayahs_read'] ?? 0,
                'ayahsMemorized' => $this->stats['ayahs_memorized'] ?? 0,
                'surahsCompleted' => $this->stats['surahs_completed'] ?? 0,
                'recitationsSubmitted' => $this->stats['recitations_submitted'] ?? 0,
                'timeSpent' => $this->stats['time_spent'] ?? 0,
                'currentStreak' => $this->stats['current_streak'] ?? 0,
                'totalPoints' => $this->stats['total_points'] ?? 0,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
