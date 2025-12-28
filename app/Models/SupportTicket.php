<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'ticket_number',
        'subject',
        'description',
        'category',
        'status',
        'priority',
        'related_enrollment_id',
        'related_class_id',
        'first_response_at',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    public function relatedEnrollment()
    {
        return $this->belongsTo(Enrollment::class, 'related_enrollment_id');
    }

    public function relatedClass()
    {
        return $this->belongsTo(ClassSession::class, 'related_class_id');
    }

    // ==================== SCOPES ====================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // ==================== HELPER METHODS ====================

    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);
    }

    public function markResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function addMessage(User $user, string $message, array $attachments = [], bool $isInternal = false): TicketMessage
    {
        $ticketMessage = $this->messages()->create([
            'user_id' => $user->id,
            'message' => $message,
            'attachments' => $attachments,
            'is_internal_note' => $isInternal,
        ]);

        // Record first response time
        if (!$this->first_response_at && $user->id !== $this->user_id) {
            $this->update(['first_response_at' => now()]);
        }

        return $ticketMessage;
    }
}
