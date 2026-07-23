<?php

namespace App\Modules\Invoice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Modules\Trip\Models\Trip;

class Invoice extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'slug',
        'invoice_number',

        'trip_id',
        'customer_id',

        'subtotal',
        'extra_km_charge',
        'late_return_charge',
        'damage_charge',
        'fuel_charge',
        'discount_amount',
        'tax_amount',
        'total_amount',

        'paid_amount',
        'due_amount',

        'status',

        'invoice_date',
        'due_date',

        'pdf_path',
        'notes',

        'generated_by',
        'generated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'invoice_date' => 'date',
        'due_date' => 'date',
        'generated_at' => 'datetime',

        'subtotal' => 'decimal:2',
        'extra_km_charge' => 'decimal:2',
        'late_return_charge' => 'decimal:2',
        'damage_charge' => 'decimal:2',
        'fuel_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
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
     * Trip for this invoice.
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
     * Customer.
     */
    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id',
            'id'
        );
    }

    /**
     * Staff who generated invoice.
     */
    public function generatedBy()
    {
        return $this->belongsTo(
            User::class,
            'generated_by',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if invoice is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice has outstanding balance.
     */
    public function hasDue(): bool
    {
        return $this->due_amount > 0;
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'due_amount' => 0,
        ]);
    }

    /**
     * Refresh payment summary.
     */
    public function refreshPaymentSummary(): void
    {
        // Total payments linked through trip
        $paid = $this->trip
            ? $this->trip->payments()->sum('amount')
            : 0;

        $due = max(
            0,
            $this->total_amount - $paid
        );

        $status = match (true) {
            $paid <= 0 => self::STATUS_ISSUED,
            $paid < $this->total_amount => self::STATUS_PARTIALLY_PAID,
            default => self::STATUS_PAID,
        };

        $this->update([
            'paid_amount' => $paid,
            'due_amount' => $due,
            'status' => $status,
        ]);
    }
}