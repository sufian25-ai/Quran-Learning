<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'guest_name',
        'guest_email',
        'user_id',
        'assigned_to',
        'status',
        'department',
        'subject',
        'last_message_at',
        'is_guest_online',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'is_guest_online' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user who started this conversation (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin/teacher assigned to this conversation.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * Get the latest message.
     */
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get display name (guest name or user name).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->guest_name ?? 'Guest';
    }

    /**
     * Check if conversation has unread messages.
     */
    public function hasUnreadMessages(): bool
    {
        return $this->messages()->where('is_read', false)
            ->where('sender_type', 'guest')
            ->exists();
    }

    /**
     * Mark all guest messages as read.
     */
    public function markAsRead(): void
    {
        $this->messages()->where('sender_type', 'guest')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
