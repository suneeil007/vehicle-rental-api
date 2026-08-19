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
        'driver_id',

        'pickup_staff_id',
        'return_staff_id',

        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',


        /*
        |--------------------------------------------------------------------------
        | Branches
        |--------------------------------------------------------------------------
        |
        | SELF DRIVE:
        |   pickup_branch_id = REQUIRED
        |   drop_branch_id   = REQUIRED
        |
        | WITH DRIVER:
        |   both NULL
        |
        */

        'pickup_branch_id',
        'drop_branch_id',


        /*
        |--------------------------------------------------------------------------
        | Locations
        |--------------------------------------------------------------------------
        |
        | SELF DRIVE:
        |   both NULL
        |
        | WITH DRIVER:
        |   both REQUIRED
        |
        */

        'pickup_location',
        'drop_location',


        'pickup_at',
        'expected_return_at',
        'actual_return_at',

        'pickup_odometer',
        'return_odometer',

        'pickup_fuel',
        'return_fuel',

        'base_amount',
        'extra_km_charge',
        'late_return_charge',
        'damage_charge',
        'fuel_charge',
        'total_amount',

        'status',

        'pickup_notes',
        'return_notes',
        'damage_notes',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'pickup_at' =>
            'datetime',

        'expected_return_at' =>
            'datetime',

        'actual_return_at' =>
            'datetime',

        'cancelled_at' =>
            'datetime',

        'base_amount' =>
            'decimal:2',

        'extra_km_charge' =>
            'decimal:2',

        'late_return_charge' =>
            'decimal:2',

        'damage_charge' =>
            'decimal:2',

        'fuel_charge' =>
            'decimal:2',

        'total_amount' =>
            'decimal:2',
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

    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id',
            'id'
        );
    }


    public function driver()
    {
        return $this->belongsTo(
            User::class,
            'driver_id',
            'id'
        );
    }


    public function pickupStaff()
    {
        return $this->belongsTo(
            User::class,
            'pickup_staff_id',
            'id'
        );
    }


    public function returnStaff()
    {
        return $this->belongsTo(
            User::class,
            'return_staff_id',
            'id'
        );
    }


    public function cancelledBy()
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by',
            'id'
        );
    }


    public function vehicle()
    {
        return $this->belongsTo(
            Vehicle::class,
            'vehicle_id',
            'id'
        );
    }


    public function pickupBranch()
    {
        return $this->belongsTo(
            Branch::class,
            'pickup_branch_id',
            'id'
        );
    }


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
    | Rental Type Helpers
    |--------------------------------------------------------------------------
    */

    public function isSelfDrive(): bool
    {
        return $this->rental_type ===
            self::RENTAL_TYPE_SELF_DRIVE;
    }


    public function isWithDriver(): bool
    {
        return $this->rental_type ===
            self::RENTAL_TYPE_WITH_DRIVER;
    }


    /*
    |--------------------------------------------------------------------------
    | Location Helpers
    |--------------------------------------------------------------------------
    */

    public function getPickupPoint(): ?string
    {
        /*
        | Self Drive = Pickup Branch
        */

        if ($this->isSelfDrive()) {

            return $this->pickupBranch?->name;
        }


        /*
        | With Driver = Pickup Location
        */

        if ($this->isWithDriver()) {

            return $this->pickup_location;
        }


        return null;
    }


    public function getDropPoint(): ?string
    {
        /*
        | Self Drive = Drop Branch
        */

        if ($this->isSelfDrive()) {

            return $this->dropBranch?->name;
        }


        /*
        | With Driver = Drop Location
        */

        if ($this->isWithDriver()) {

            return $this->drop_location;
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | State Helpers
    |--------------------------------------------------------------------------
    */

    public function isScheduled(): bool
    {
        return $this->status ===
            self::STATUS_SCHEDULED;
    }


    public function isActive(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PICKED_UP,
                self::STATUS_ON_TRIP,
            ],
            true
        );
    }


    public function isCompleted(): bool
    {
        return $this->status ===
            self::STATUS_COMPLETED;
    }


    public function isCancelled(): bool
    {
        return $this->status ===
            self::STATUS_CANCELLED;
    }


    public function canBeCancelled(): bool
    {
        return $this->isScheduled();
    }


    /*
    |--------------------------------------------------------------------------
    | Branch Helpers
    |--------------------------------------------------------------------------
    */

    public function hasPickupBranch(): bool
    {
        return !empty(
            $this->pickup_branch_id
        );
    }


    public function hasDropBranch(): bool
    {
        return !empty(
            $this->drop_branch_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Trip Validation Helpers
    |--------------------------------------------------------------------------
    */

    public function hasRequiredLocations(): bool
    {
        /*
        | Self Drive does NOT need locations.
        */

        if ($this->isSelfDrive()) {
            return true;
        }


        /*
        | With Driver needs both locations.
        */

        if ($this->isWithDriver()) {

            return !empty($this->pickup_location)
                &&
                !empty($this->drop_location);
        }


        return false;
    }


    public function hasRequiredPickupBranch(): bool
    {
        /*
        | Only Self Drive requires pickup branch.
        */

        if ($this->isSelfDrive()) {

            return !empty(
                $this->pickup_branch_id
            );
        }


        /*
        | With Driver does not require branch.
        */

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Computed Attributes
    |--------------------------------------------------------------------------
    */

    public function getDistanceAttribute(): ?int
    {
        if (
            $this->pickup_odometer !== null
            &&
            $this->return_odometer !== null
        ) {

            return $this->return_odometer
                -
                $this->pickup_odometer;
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(
            \App\Modules\Payment\Models\Payment::class,
            'trip_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */

    public function booking()
    {
        return $this->hasOne(
            \App\Modules\Booking\Models\Booking::class,
            'trip_id',
            'id'
        );
    }
}