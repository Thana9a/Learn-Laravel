<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orderDiscounts(): HasMany
    {
        return $this->hasMany(OrderDiscount::class);
    }

    /**
     * Check if the discount is valid and applicable right now.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount based on order subtotal.
     */
    public function calculateDiscountAmount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $amount = ($subtotal * (float) $this->value) / 100.0;
        } else {
            $amount = (float) $this->value;
        }

        return round(min($amount, $subtotal), 2);
    }
}
