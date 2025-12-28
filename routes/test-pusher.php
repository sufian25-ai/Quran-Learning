<?php

use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;
use App\Models\ChatMessage;

// Test route to manually trigger a Pusher event
Route::get('/test-pusher', function () {
    // Create a fake message for testing
    $testMessage = new ChatMessage([
        'id' => 999,
        'conversation_id' => 1,
        'sender_type' => 'system',
        'message' => 'This is a test message from Pusher at ' . now()->format('H:i:s'),
        'is_read' => false,
        'created_at' => now(),
    ]);

    // Broadcast the event
    try {
        broadcast(new MessageSent($testMessage, 'test-session-id'));

        return response()->json([
            'success' => true,
            'message' => 'Event broadcasted successfully!',
            'time' => now()->format('H:i:s'),
            'channel' => 'chat.test-session-id',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});
