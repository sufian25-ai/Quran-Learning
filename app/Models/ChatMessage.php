<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'attachments' => 'array',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the conversation this message belongs to.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Get the sender user (if not a guest).
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get sender display name.
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender) {
            return $this->sender->name;
        }

        if ($this->sender_type === 'guest') {
            return $this->conversation?->guest_name ?? 'Guest';
        }

        if ($this->sender_type === 'system') {
            return 'System';
        }

        return 'Support';
    }

    /**
     * Check if message is from guest.
     */
    public function isFromGuest(): bool
    {
        return $this->sender_type === 'guest';
    }

    /**
     * Check if message is from admin/teacher.
     */
    public function isFromSupport(): bool
    {
        return in_array($this->sender_type, ['admin', 'teacher', 'user']);
    }
}
