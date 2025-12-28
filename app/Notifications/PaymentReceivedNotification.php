<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Payment $payment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->payment->amount, 2);
        $currency = $this->payment->currency ?? 'USD';
        $courseName = $this->payment->enrollment?->course?->title ?? 'your course';

        return (new MailMessage)
            ->subject("Payment Received - {$currency} {$amount}")
            ->greeting("Jazak Allah Khair, {$notifiable->name}!")
            ->line("We've received your payment of **{$currency} {$amount}** for **{$courseName}**.")
            ->line("**Transaction ID:** {$this->payment->transaction_id}")
            ->line("**Date:** " . $this->payment->paid_at?->format('F j, Y'))
            ->action('View Receipt', url("/payments/{$this->payment->id}"))
            ->line('Thank you for your trust in us!');
    }

    public function toArray(object $notifiable): array
    {
        $amount = number_format($this->payment->amount, 2);
        $currency = $this->payment->currency ?? 'USD';

        return [
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => "Payment of {$currency} {$amount} received successfully",
            'action_url' => "/payments/{$this->payment->id}",
            'action_text' => 'View Receipt',
            'payment_id' => $this->payment->id,
        ];
    }
}
