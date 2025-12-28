<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enrollment_id',
        'transaction_id',
        'gateway',
        'gateway_transaction_id',
        'amount',
        'gateway_fee',
        'currency',
        'status',
        'type',
        'gateway_response',
        'metadata',
        'refund_amount',
        'refund_reason',
        'paid_at',
        'refunded_at',
        'invoice_number',
        'invoice_url',
    ];

    protected function casts(): array
    {
        return [
            'gateway_response' => 'array',
            'metadata' => 'array',
            'amount' => 'decimal:2',
            'gateway_fee' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = 'TXN-' . strtoupper(Str::random(12));
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user who made this payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the enrollment for this payment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope by gateway.
     */
    public function scopeGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->isCompleted() && $this->refund_amount < $this->amount;
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get net amount after gateway fees.
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->gateway_fee;
    }

    /**
     * Mark payment as completed.
     */
    public function markCompleted($gatewayTransactionId = null): void
    {
        $this->update([
            'status' => 'completed',
            'gateway_transaction_id' => $gatewayTransactionId ?? $this->gateway_transaction_id,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markFailed($reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason]),
        ]);
    }

    /**
     * Process refund.
     */
    public function processRefund(float $amount, string $reason = null): void
    {
        $this->update([
            'status' => $amount >= $this->amount ? 'refunded' : 'partially_refunded',
            'refund_amount' => $this->refund_amount + $amount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);
    }

    /**
     * Generate invoice number.
     */
    public function generateInvoiceNumber(): string
    {
        $invoice = 'INV-' . date('Ymd') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        $this->update(['invoice_number' => $invoice]);

        return $invoice;
    }
}
