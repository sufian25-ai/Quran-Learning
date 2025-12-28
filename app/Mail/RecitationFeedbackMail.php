<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecitationFeedbackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $submission;
    public $feedback;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $submission, $feedback)
    {
        $this->user = $user;
        $this->submission = $submission;
        $this->feedback = $feedback;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎤 Your Recitation Has Been Reviewed!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.recitation-feedback',
            with: [
                'userName' => $this->user->name,
                'surahName' => $this->submission->surah?->name_english ?? 'Surah',
                'ayahRange' => $this->submission->ayah_range,
                'overallRating' => $this->feedback->overall_rating,
                'pronunciationScore' => $this->feedback->pronunciation_score,
                'tajweedScore' => $this->feedback->tajweed_score,
                'fluencyScore' => $this->feedback->fluency_score,
                'feedbackText' => $this->feedback->feedback_text,
                'teacherName' => $this->feedback->teacher?->name ?? 'Your Teacher',
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
