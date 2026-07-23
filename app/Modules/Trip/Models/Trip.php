<?php

namespace App\Modules\Trip\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Modules\Branch\Models\Branch;
use App\Modules\Vehicle\Models\Vehicle;

class Trip extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_ON_TRIP = 'on_trip';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [

        'slug',

        // Customer / Vehicle
        'customer_id',
        'vehicle_id',

        // Rental
        'rental_type',
        'driver_id',

        // Staff
        'pickup_staff_id',
        'return_staff_id',

        // Cancellation audit
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',

        // Branches
        'pickup_branch_id',
        'drop_branch_id',

        // Schedule
        'pickup_at',
        'expected_return_at',
        'actual_return_at',

        // Odometer
        'pickup_odometer',
        'return_odometer',

        // Fuel
        'pickup_fuel',
        'return_fuel',

        // Billing
        'base_amount',
        'extra_km_charge',
        'late_return_charge',
        'damage_charge',
        'fuel_charge',
        'total_amount',

        // Status
        'status',

        // Notes
        'pickup_notes',
        'return_notes',
        'damage_notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */
    protected $casts = [

        'pickup_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'actual_return_at' => 'datetime',
        'cancelled_at' => 'datetime',

        'base_amount' => 'decimal:2',
        'extra_km_charge' => 'decimal:2',
        'late_return_charge' => 'decimal:2',
        'damage_charge' => 'decimal:2',
        'fuel_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
     * Customer who rented the vehicle.
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
     * Driver assigned to the trip.
     */
    public function driver()
    {
        return $this->belongsTo(
            User::class,
            'driver_id',
            'id'
        );
    }

    /**
     * Staff who handed over the vehicle.
     */
    public function pickupStaff()
    {
        return $this->belongsTo(
            User::class,
            'pickup_staff_id',
            'id'
        );
    }

    /**
     * Staff who received the vehicle back.
     */
    public function returnStaff()
    {
        return $this->belongsTo(
            User::class,
            'return_staff_id',
            'id'
        );
    }

    /**
     * User who cancelled the trip.
     */
    public function cancelledBy()
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by',
            'id'
        );
    }

    /**
     * Vehicle used in the trip.
     */
    public function vehicle()
    {
        return $this->belongsTo(
            Vehicle::class,
            'vehicle_id',
            'id'
        );
    }

    /**
     * Pickup branch.
     */
    public function pickupBranch()
    {
        return $this->belongsTo(
            Branch::class,
            'pickup_branch_id',
            'id'
        );
    }

    /**
     * Drop branch.
     */
    public function dropBranch()
    {
        return $this->belongsTo(
            Branch::class,
            'drop_branch_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | State Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if trip is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if trip is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_PICKED_UP,
            self::STATUS_ON_TRIP,
        ]);
    }

    /**
     * Check if trip is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if trip is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if trip can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return $this->isScheduled();
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate traveled distance in kilometers.
     */
    public function getDistanceAttribute(): ?int
    {
        if (
            $this->pickup_odometer !== null &&
            $this->return_odometer !== null
        ) {
            return $this->return_odometer
                - $this->pickup_odometer;
        }

        return null;
    }


    /**
     * Payments made for this trip.
     */
    public function payments()
    {
        return $this->hasMany(
            \App\Modules\Payment\Models\Payment::class,
            'trip_id',
            'id'
        );
    }
}