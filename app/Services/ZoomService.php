<?php

namespace App\Services;

use App\Models\ClassSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ZoomService
{
    private string $accountId;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
    }

    /**
     * Get access token using Server-to-Server OAuth
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('zoom_access_token', 3500, function () {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('Failed to get Zoom access token', [
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to authenticate with Zoom');
        });
    }

    /**
     * Create a Zoom meeting for a class
     */
    public function createMeeting(ClassSession $class): array
    {
        try {
            $response = Http::withToken($this->getAccessToken())
                ->post('https://api.zoom.us/v2/users/me/meetings', [
                    'topic' => $class->title,
                    'type' => 2, // Scheduled meeting
                    'start_time' => $class->scheduled_at->toIso8601String(),
                    'duration' => $class->duration_minutes,
                    'timezone' => 'UTC',
                    'password' => $this->generateMeetingPassword(),
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => false,
                        'mute_upon_entry' => true,
                        'waiting_room' => true,
                        'audio' => 'both',
                        'auto_recording' => 'cloud',
                        'alternative_hosts' => '',
                        'approval_type' => 2, // No registration required
                    ],
                ]);

            if ($response->successful()) {
                $meeting = $response->json();

                // Update class with Zoom details
                $class->setZoomMeeting([
                    'id' => $meeting['id'],
                    'join_url' => $meeting['join_url'],
                    'start_url' => $meeting['start_url'],
                    'password' => $meeting['password'] ?? null,
                ]);

                return [
                    'success' => true,
                    'meeting_id' => $meeting['id'],
                    'join_url' => $meeting['join_url'],
                    'start_url' => $meeting['start_url'],
                ];
            }

            Log::error('Failed to create Zoom meeting', [
                'class_id' => $class->id,
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create Zoom meeting',
            ];

        } catch (\Exception $e) {
            Log::error('Zoom API error', [
                'class_id' => $class->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing Zoom meeting
     */
    public function updateMeeting(ClassSession $class): array
    {
        if (!$class->zoom_meeting_id) {
            return $this->createMeeting($class);
        }

        try {
            $response = Http::withToken($this->getAccessToken())
                ->patch("https://api.zoom.us/v2/meetings/{$class->zoom_meeting_id}", [
                    'topic' => $class->title,
                    'start_time' => $class->scheduled_at->toIso8601String(),
                    'duration' => $class->duration_minutes,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'meeting_id' => $class->zoom_meeting_id,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to update meeting',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a Zoom meeting
     */
    public function deleteMeeting(string $meetingId): array
    {
        try {
            $response = Http::withToken($this->getAccessToken())
                ->delete("https://api.zoom.us/v2/meetings/{$meetingId}");

            return [
                'success' => $response->successful(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get meeting recordings
     */
    public function getRecordings(string $meetingId): array
    {
        try {
            $response = Http::withToken($this->getAccessToken())
                ->get("https://api.zoom.us/v2/meetings/{$meetingId}/recordings");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'recordings' => $data['recording_files'] ?? [],
                    'share_url' => $data['share_url'] ?? null,
                ];
            }

            return [
                'success' => false,
                'recordings' => [],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get meeting participants
     */
    public function getParticipants(string $meetingId): array
    {
        try {
            $response = Http::withToken($this->getAccessToken())
                ->get("https://api.zoom.us/v2/past_meetings/{$meetingId}/participants");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'participants' => $response->json()['participants'] ?? [],
                ];
            }

            return [
                'success' => false,
                'participants' => [],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a random meeting password
     */
    protected function generateMeetingPassword(): string
    {
        return substr(str_shuffle('abcdefghijkmnpqrstuvwxyz23456789'), 0, 6);
    }

    /**
     * Handle Zoom webhook events
     */
    public function handleWebhook(array $payload): void
    {
        $event = $payload['event'] ?? null;
        $data = $payload['payload']['object'] ?? [];

        switch ($event) {
            case 'meeting.started':
                $this->handleMeetingStarted($data);
                break;

            case 'meeting.ended':
                $this->handleMeetingEnded($data);
                break;

            case 'recording.completed':
                $this->handleRecordingCompleted($data);
                break;

            case 'meeting.participant_joined':
                $this->handleParticipantJoined($data);
                break;
        }
    }

    protected function handleMeetingStarted(array $data): void
    {
        $class = ClassSession::where('zoom_meeting_id', $data['id'])->first();

        if ($class) {
            $class->start();
        }
    }

    protected function handleMeetingEnded(array $data): void
    {
        $class = ClassSession::where('zoom_meeting_id', $data['id'])->first();

        if ($class) {
            $class->end();

            // Update attendee count
            $class->update([
                'attendee_count' => $data['participant_count'] ?? 0,
            ]);
        }
    }

    protected function handleRecordingCompleted(array $data): void
    {
        $class = ClassSession::where('zoom_meeting_id', $data['id'])->first();

        if ($class && isset($data['share_url'])) {
            $class->update([
                'recording_url' => $data['share_url'],
            ]);
        }
    }

    protected function handleParticipantJoined(array $data): void
    {
        // Could be used for auto-attendance tracking
        Log::info('Participant joined', [
            'meeting_id' => $data['id'],
            'participant' => $data['participant'] ?? null,
        ]);
    }
}
