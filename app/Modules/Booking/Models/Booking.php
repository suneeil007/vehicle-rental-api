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
        | Branches
        |
        | Self Drive:
        |   pickup_branch_id = required
        |   drop_branch_id   = required
        |
        | With Driver:
        |   both NULL
        */

        'pickup_branch_id',
        'drop_branch_id',

        /*
        | Locations
        |
        | Self Drive:
        |   both NULL
        |
        | With Driver:
        |   both required
        */

        'pickup_location',
        'drop_location',

        'pickup_at',
        'expected_return_at',

        'quoted_amount',
        'discount_amount',
        'final_amount',

        'approved_by',
        'approved_at',

        'trip_id',

        'status',

        'customer_notes',
        'admin_notes',
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

        'approved_at' =>
            'datetime',

        'quoted_amount' =>
            'decimal:2',

        'discount_amount' =>
            'decimal:2',

        'final_amount' =>
            'decimal:2',
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

    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id',
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


    public function approvedBy()
    {
        return $this->belongsTo(
            User::class,
            'approved_by',
            'id'
        );
    }


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
        | With Driver = Customer Location
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
        | With Driver = Customer Location
        */

        if ($this->isWithDriver()) {
            return $this->drop_location;
        }

        return null;
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
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
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
        return $this->status ===
            self::STATUS_PENDING;
    }


    public function canBeRejected(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
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
        return $this->status ===
            self::STATUS_PENDING;
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


    /*
    |--------------------------------------------------------------------------
    | Trip Helpers
    |--------------------------------------------------------------------------
    */

    public function hasTrip(): bool
    {
        return !empty($this->trip_id);
    }


    public function canCreateTrip(): bool
    {
        return $this->status ===
            self::STATUS_APPROVED
            &&
            empty($this->trip_id);
    }


    /*
    |--------------------------------------------------------------------------
    | Convenience Helpers
    |--------------------------------------------------------------------------
    */

    public function getRentalTypeLabel(): string
    {
        return match ($this->rental_type) {

            self::RENTAL_TYPE_SELF_DRIVE =>
                'Self Drive',

            self::RENTAL_TYPE_WITH_DRIVER =>
                'With Driver',

            default =>
                'Unknown',
        };
    }


    public function getStatusLabel(): string
    {
        return match ($this->status) {

            self::STATUS_PENDING =>
                'Pending',

            self::STATUS_APPROVED =>
                'Approved',

            self::STATUS_REJECTED =>
                'Rejected',

            self::STATUS_CANCELLED =>
                'Cancelled',

            self::STATUS_TRIP_CREATED =>
                'Trip Created',

            self::STATUS_COMPLETED =>
                'Completed',

            default =>
                'Unknown',
        };
    }
}