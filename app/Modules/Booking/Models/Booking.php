<?php

namespace App\Modules\Booking\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Modules\Branch\Models\Branch;
use App\Modules\Trip\Models\Trip;
use App\Modules\Vehicle\Models\Vehicle;

class Booking extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_TRIP_CREATED = 'trip_created';
    public const STATUS_COMPLETED = 'completed';


    /*
    |--------------------------------------------------------------------------
    | Rental Type Constants
    |--------------------------------------------------------------------------
    */

    public const RENTAL_TYPE_SELF_DRIVE = 'self_drive';
    public const RENTAL_TYPE_WITH_DRIVER = 'with_driver';


    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'slug',

        'customer_id',
        'vehicle_id',

        'rental_type',

        /*
        |--------------------------------------------------------------------------
        | Branch based pickup/drop
        |--------------------------------------------------------------------------
        |
        | Used for WITH DRIVER bookings.
        |
        */

        'pickup_branch_id',
        'drop_branch_id',

        /*
        |--------------------------------------------------------------------------
        | Customer location based pickup/drop
        |--------------------------------------------------------------------------
        |
        | Used for SELF DRIVE bookings.
        |
        */

        'pickup_location',
        'drop_location',

        /*
        |--------------------------------------------------------------------------
        | Dates
        |--------------------------------------------------------------------------
        */

        'pickup_at',
        'expected_return_at',

        /*
        |--------------------------------------------------------------------------
        | Amounts
        |--------------------------------------------------------------------------
        */

        'quoted_amount',
        'discount_amount',
        'final_amount',

        /*
        |--------------------------------------------------------------------------
        | Approval
        |--------------------------------------------------------------------------
        */

        'approved_by',
        'approved_at',

        /*
        |--------------------------------------------------------------------------
        | Trip
        |--------------------------------------------------------------------------
        */

        'trip_id',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | Notes
        |--------------------------------------------------------------------------
        */

        'customer_notes',
        'admin_notes',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'pickup_at' => 'datetime',

        'expected_return_at' => 'datetime',

        'approved_at' => 'datetime',

        'quoted_amount' => 'decimal:2',

        'discount_amount' => 'decimal:2',

        'final_amount' => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Route Binding
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
     * Customer who created the booking.
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
     * Vehicle assigned to the booking.
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
     *
     * Used for WITH DRIVER bookings.
     *
     * NULL for SELF DRIVE bookings.
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
     *
     * Used for WITH DRIVER bookings.
     *
     * NULL for SELF DRIVE bookings.
     */
    public function dropBranch()
    {
        return $this->belongsTo(
            Branch::class,
            'drop_branch_id',
            'id'
        );
    }


    /**
     * User who approved the booking.
     */
    public function approvedBy()
    {
        return $this->belongsTo(
            User::class,
            'approved_by',
            'id'
        );
    }


    /**
     * Trip created from this booking.
     */
    public function trip()
    {
        return $this->belongsTo(
            Trip::class,
            'trip_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Rental Type Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether this is a Self Drive booking.
     */
    public function isSelfDrive(): bool
    {
        return $this->rental_type === self::RENTAL_TYPE_SELF_DRIVE;
    }


    /**
     * Check whether this is a With Driver booking.
     */
    public function isWithDriver(): bool
    {
        return $this->rental_type === self::RENTAL_TYPE_WITH_DRIVER;
    }


    /*
    |--------------------------------------------------------------------------
    | Booking Location Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get the pickup point depending on rental type.
     *
     * WITH DRIVER:
     *     Pickup branch name
     *
     * SELF DRIVE:
     *     Customer pickup location
     */
    public function getPickupPoint(): ?string
    {
        if ($this->isWithDriver()) {
            return $this->pickupBranch?->name;
        }

        return $this->pickup_location;
    }


    /**
     * Get the drop point depending on rental type.
     *
     * WITH DRIVER:
     *     Drop branch name
     *
     * SELF DRIVE:
     *     Customer drop location
     */
    public function getDropPoint(): ?string
    {
        if ($this->isWithDriver()) {
            return $this->dropBranch?->name;
        }

        return $this->drop_location;
    }


    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }


    public function isApproved(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_APPROVED,
                self::STATUS_TRIP_CREATED,
                self::STATUS_COMPLETED,
            ],
            true
        );
    }


    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }


    public function canBeCancelled(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PENDING,
                self::STATUS_APPROVED,
            ],
            true
        );
    }


    public function canBeRestored___(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }


    public function canBeRestored(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_CANCELLED,
                self::STATUS_REJECTED,
            ],
            true
        );
    }


    public function canBeUpdated(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }


    public function canBeDeleted(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PENDING,
                self::STATUS_REJECTED,
                self::STATUS_CANCELLED,
            ],
            true
        );
    }
}