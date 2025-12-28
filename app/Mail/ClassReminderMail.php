<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClassReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $classSession;
    public $startsIn;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $classSession, $startsIn = '30 minutes')
    {
        $this->user = $user;
        $this->classSession = $classSession;
        $this->startsIn = $startsIn;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Class Reminder - Your Quran class starts in ' . $this->startsIn,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.class-reminder',
            with: [
                'userName' => $this->user->name,
                'classTitle' => $this->classSession->batch?->course?->title ?? 'Quran Class',
                'teacherName' => $this->classSession->teacher?->name ?? 'Your Teacher',
                'scheduledTime' => $this->classSession->scheduled_at?->format('h:i A'),
                'scheduledDate' => $this->classSession->scheduled_at?->format('l, F j, Y'),
                'meetingLink' => $this->classSession->meeting_link ?? '#',
                'startsIn' => $this->startsIn,
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
