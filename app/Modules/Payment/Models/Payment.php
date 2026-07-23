<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Modules\Trip\Models\Trip;
use App\Modules\Booking\Models\Booking;

class Payment extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */
    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    /*
    |--------------------------------------------------------------------------
    | Type Constants
    |--------------------------------------------------------------------------
    */
    public const TYPE_ADVANCE = 'advance';
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_FINAL   = 'final';
    public const TYPE_REFUND  = 'refund';

    /*
    |--------------------------------------------------------------------------
    | Payment Method Constants
    |--------------------------------------------------------------------------
    */
    public const METHOD_CASH          = 'cash';
    public const METHOD_CARD          = 'card';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_ESEWA         = 'esewa';
    public const METHOD_KHALTI        = 'khalti';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [

        'slug',

        // Relations
        'booking_id',
        'trip_id',

        // Payment details
        'amount',
        'type',
        'payment_method',
        'transaction_reference',
        'status',

        // Staff tracking
        'received_by',
        'paid_at',

        // Additional info
        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [

        'amount' => 'decimal:2',

        'paid_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Related booking.
     */
    public function booking()
    {
        return $this->belongsTo(
            Booking::class,
            'booking_id',
            'id'
        );
    }

    /**
     * Related trip.
     */
    public function trip()
    {
        return $this->belongsTo(
            Trip::class,
            'trip_id',
            'id'
        );
    }

    /**
     * Staff who received the payment.
     */
    public function receivedBy()
    {
        return $this->belongsTo(
            User::class,
            'received_by',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if payment was refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Check if this is an advance payment.
     */
    public function isAdvance(): bool
    {
        return $this->type === self::TYPE_ADVANCE;
    }

    /**
     * Check if this is a security deposit.
     */
    public function isDeposit(): bool
    {
        return $this->type === self::TYPE_DEPOSIT;
    }

    /**
     * Check if this is a final settlement payment.
     */
    public function isFinal(): bool
    {
        return $this->type === self::TYPE_FINAL;
    }

    /**
     * Check if this is a refund.
     */
    public function isRefund(): bool
    {
        return $this->type === self::TYPE_REFUND;
    }

    /**
     * Mark payment as paid.
     */
    public function markAsPaid(?int $staffId = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'received_by' => $staffId,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    /**
     * Mark payment as refunded.
     */
    public function markAsRefunded(): void
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
        ]);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'NPR ' . number_format(
            (float) $this->amount,
            2
        );
    }
}