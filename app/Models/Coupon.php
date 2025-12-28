<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_purchase_amount',
        'max_discount_amount',
        'max_uses',
        'max_uses_per_user',
        'used_count',
        'applicable_courses',
        'applicable_categories',
        'valid_from',
        'valid_until',
        'is_active',
        'is_first_order_only',
    ];

    protected function casts(): array
    {
        return [
            'applicable_courses' => 'array',
            'applicable_categories' => 'array',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
            'is_first_order_only' => 'boolean',
            'value' => 'decimal:2',
            'min_purchase_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    // ==================== HELPER METHODS ====================

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from > now()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < now()) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function canBeUsedByUser(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check per-user limit
        $userUsages = $this->usages()->where('user_id', $user->id)->count();
        if ($userUsages >= $this->max_uses_per_user) {
            return false;
        }

        // Check first order only
        if ($this->is_first_order_only && $user->enrollments()->exists()) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = $amount * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        // Apply max discount cap
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount);
    }

    public function recordUsage(User $user, ?Enrollment $enrollment = null, float $discount = 0): void
    {
        CouponUsage::create([
            'coupon_id' => $this->id,
            'user_id' => $user->id,
            'enrollment_id' => $enrollment?->id,
            'discount_applied' => $discount,
        ]);

        $this->increment('used_count');
    }
}
