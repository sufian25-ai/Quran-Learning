<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioSmsService
{
    protected string $accountSid;
    protected string $authToken;
    protected string $fromNumber;
    protected bool $enabled;

    public function __construct()
    {
        $this->accountSid = config('services.twilio.account_sid', '');
        $this->authToken = config('services.twilio.auth_token', '');
        $this->fromNumber = config('services.twilio.from_number', '');
        $this->enabled = config('services.twilio.enabled', false);
    }

    /**
     * Send an SMS message.
     */
    public function send(string $to, string $message): array
    {
        if (!$this->enabled) {
            Log::info("SMS to {$to}: {$message} (Twilio disabled)");
            return ['success' => true, 'message' => 'SMS logged (Twilio disabled)'];
        }

        if (empty($this->accountSid) || empty($this->authToken) || empty($this->fromNumber)) {
            Log::warning('Twilio credentials not configured');
            return ['success' => false, 'message' => 'Twilio not configured'];
        }

        try {
            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->asForm()
                ->post(
                    "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json",
                    [
                        'From' => $this->fromNumber,
                        'To' => $this->formatPhoneNumber($to),
                        'Body' => $message,
                    ]
                );

            if ($response->successful()) {
                Log::info("SMS sent to {$to}");
                return [
                    'success' => true,
                    'message_sid' => $response->json('sid'),
                ];
            }

            Log::error("SMS failed to {$to}: " . $response->body());
            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Failed to send SMS',
            ];
        } catch (\Exception $e) {
            Log::error("SMS exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send class reminder SMS.
     */
    public function sendClassReminder(string $to, string $studentName, string $classTitle, string $time): array
    {
        $message = "Assalamu Alaikum {$studentName}! Your class '{$classTitle}' starts at {$time}. May your learning be blessed. - QuranLearn";
        return $this->send($to, $message);
    }

    /**
     * Send enrollment confirmation SMS.
     */
    public function sendEnrollmentConfirmation(string $to, string $studentName, string $courseName): array
    {
        $message = "Alhamdulillah {$studentName}! You're enrolled in '{$courseName}'. Welcome to QuranLearn!";
        return $this->send($to, $message);
    }

    /**
     * Send payment confirmation SMS.
     */
    public function sendPaymentConfirmation(string $to, string $amount, string $courseName): array
    {
        $message = "Jazak Allah Khair! Payment of {$amount} received for '{$courseName}'. - QuranLearn";
        return $this->send($to, $message);
    }

    /**
     * Format phone number to E.164 format.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // If doesn't start with +, assume it needs country code
        if (!str_starts_with($phone, '+')) {
            // Default to US country code if none provided
            if (!str_starts_with($phone, '1') && strlen($phone) === 10) {
                $phone = '+1' . $phone;
            } else {
                $phone = '+' . $phone;
            }
        }

        return $phone;
    }
}
