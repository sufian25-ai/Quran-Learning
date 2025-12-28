<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Start a new chat conversation (Public - no auth required)
     */
    public function startConversation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
            'department' => 'nullable|in:general,teacher,admin,support',
        ]);

        $sessionId = $request->input('session_id', Str::uuid()->toString());

        // Check for existing active conversation
        $conversation = ChatConversation::where('session_id', $sessionId)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if (!$conversation) {
            $conversation = ChatConversation::create([
                'session_id' => $sessionId,
                'guest_name' => $validated['name'],
                'guest_email' => $validated['email'],
                'user_id' => auth()->id(),
                'status' => 'pending',
                'department' => $validated['department'] ?? 'general',
                'last_message_at' => now(),
                'is_guest_online' => true,
            ]);

            // System welcome message
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'system',
                'message' => 'Welcome to QuranLearn support! Our team will respond shortly.',
                'is_read' => true,
            ]);
        }

        // Add the user's message
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => auth()->check() ? 'user' : 'guest',
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast event
        broadcast(new \App\Events\MessageSent($message, $sessionId));

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Send a message in existing conversation (Public)
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'required|string',
        ]);

        $conversation = ChatConversation::where('id', $conversationId)
            ->where('session_id', $validated['session_id'])
            ->firstOrFail();

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => auth()->check() ? 'user' : 'guest',
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'is_guest_online' => true,
        ]);

        // Broadcast event
        broadcast(new \App\Events\MessageSent($message, $conversation->session_id))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Get messages for a conversation (Public - with session validation)
     */
    public function getMessages(Request $request, $conversationId)
    {
        $sessionId = $request->query('session_id');
        $after = $request->query('after', 0);

        $conversation = ChatConversation::where('id', $conversationId)
            ->where('session_id', $sessionId)
            ->firstOrFail();

        $messages = $conversation->messages()
            ->where('id', '>', $after)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at->toISOString(),
            ]);

        // Mark admin/teacher messages as read
        $conversation->messages()
            ->whereIn('sender_type', ['admin', 'teacher', 'system'])
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'status' => $conversation->status,
        ]);
    }

    /**
     * Admin: List all conversations
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = ChatConversation::with(['latestMessage', 'assignedUser:id,name'])
            ->orderByDesc('last_message_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $conversations = $query->paginate(20);

        return Inertia::render('Admin/Chat/Index', [
            'conversations' => $conversations->through(fn($c) => [
                'id' => $c->id,
                'guest_name' => $c->display_name,
                'guest_email' => $c->guest_email,
                'status' => $c->status,
                'department' => $c->department,
                'session_id' => $c->session_id, // Added session_id for listener
                'assigned_to' => $c->assignedUser?->name,
                'last_message' => $c->latestMessage?->message,
                'last_message_at' => $c->last_message_at?->diffForHumans(),
                'has_unread' => $c->hasUnreadMessages(),
                'created_at' => $c->created_at->format('M d, Y h:i A'),
            ]),
            'filters' => ['status' => $status],
        ]);
    }

    /**
     * Admin: View single conversation
     */
    public function adminShow($conversationId)
    {
        $conversation = ChatConversation::with(['messages.sender', 'assignedUser'])
            ->findOrFail($conversationId);

        // Mark as read
        $conversation->markAsRead();

        // Auto-assign if not assigned
        if (!$conversation->assigned_to) {
            $conversation->update([
                'assigned_to' => auth()->id(),
                'status' => 'active',
            ]);
        }

        return Inertia::render('Admin/Chat/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'session_id' => $conversation->session_id, // Added session_id for channel
                'guest_name' => $conversation->display_name,
                'guest_email' => $conversation->guest_email,
                'status' => $conversation->status,
                'department' => $conversation->department,
                'assigned_to' => $conversation->assignedUser?->name,
                'is_guest_online' => $conversation->is_guest_online,
                'created_at' => $conversation->created_at->format('M d, Y h:i A'),
            ],
            'messages' => $conversation->messages->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at->toISOString(),
            ]),
        ]);
    }

    /**
     * Admin: Reply to conversation
     */
    public function adminReply(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $conversation = ChatConversation::findOrFail($conversationId);

        $user = auth()->user();
        $senderType = $user->hasRole('admin') ? 'admin' : 'teacher';

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'sender_id' => $user->id,
            'message' => $validated['message'],
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => 'active',
        ]);

        // Broadcast event
        broadcast(new \App\Events\MessageSent($message, $conversation->session_id));

        return back()->with('success', 'Message sent!');
    }

    /**
     * Admin: Close conversation
     */
    public function closeConversation($conversationId)
    {
        $conversation = ChatConversation::findOrFail($conversationId);

        $conversation->update(['status' => 'closed']);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'system',
            'message' => 'This conversation has been closed. Thank you for contacting us!',
        ]);

        // Broadcast event
        broadcast(new \App\Events\MessageSent($message, $conversation->session_id));

        return back()->with('success', 'Conversation closed.');
    }

    /**
     * Admin: Get new messages (for polling)
     */
    public function adminGetMessages(Request $request, $conversationId)
    {
        $after = $request->query('after', 0);

        $conversation = ChatConversation::findOrFail($conversationId);

        $messages = $conversation->messages()
            ->where('id', '>', $after)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at->toISOString(),
            ]);

        return response()->json([
            'messages' => $messages,
            'is_guest_online' => $conversation->is_guest_online,
        ]);
    }
}
