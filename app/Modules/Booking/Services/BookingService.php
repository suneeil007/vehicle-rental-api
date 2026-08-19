<?php

namespace App\Modules\Booking\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;

use App\Modules\Booking\Models\Booking;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;
use App\Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;

class BookingService
{
    public function __construct(
        protected BookingRepositoryInterface $repository,
        protected TripService $tripService
    ) {}


    /*
    |--------------------------------------------------------------------------
    | List bookings
    |--------------------------------------------------------------------------
    */

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }


    /*
    |--------------------------------------------------------------------------
    | Show booking
    |--------------------------------------------------------------------------
    */

    public function getById(int $id): Booking
    {
        $booking = $this->repository->getById($id);

        if (!$booking) {
            throw new NotFoundException(
                'Booking not found.'
            );
        }

        return $booking;
    }


    /*
    |--------------------------------------------------------------------------
    | Create booking
    |--------------------------------------------------------------------------
    |
    | SELF DRIVE
    |
    | pickup_branch_id = REQUIRED
    | drop_branch_id   = REQUIRED
    | pickup_location  = NULL
    | drop_location    = NULL
    |
    | WITH DRIVER
    |
    | pickup_branch_id = NULL
    | drop_branch_id   = NULL
    | pickup_location  = REQUIRED
    | drop_location    = REQUIRED
    |
    */

    public function create(array $data): Booking
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Rental type
            |--------------------------------------------------------------------------
            */

            $rentalType = $data['rental_type']
                ?? Booking::RENTAL_TYPE_SELF_DRIVE;


            /*
            |--------------------------------------------------------------------------
            | Validate rental type
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                $rentalType,
                [
                    Booking::RENTAL_TYPE_SELF_DRIVE,
                    Booking::RENTAL_TYPE_WITH_DRIVER,
                ],
                true
            )) {
                throw new ConflictException(
                    'Invalid rental type.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Normalize fields
            |--------------------------------------------------------------------------
            */

            $pickupBranchId = null;
            $dropBranchId = null;

            $pickupLocation = null;
            $dropLocation = null;


            /*
            |--------------------------------------------------------------------------
            | SELF DRIVE
            |--------------------------------------------------------------------------
            |
            | Pickup = Branch
            | Drop   = Branch
            |
            */

            if (
                $rentalType ===
                Booking::RENTAL_TYPE_SELF_DRIVE
            ) {

                $pickupBranchId =
                    $data['pickup_branch_id']
                    ?? null;

                $dropBranchId =
                    $data['drop_branch_id']
                    ?? null;


                /*
                |--------------------------------------------------------------------------
                | Pickup branch required
                |--------------------------------------------------------------------------
                */

                if (!$pickupBranchId) {

                    throw new ConflictException(
                        'Pickup branch is required for a self drive booking.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Drop branch required
                |--------------------------------------------------------------------------
                */

                if (!$dropBranchId) {

                    throw new ConflictException(
                        'Drop branch is required for a self drive booking.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Locations are NOT allowed
                |--------------------------------------------------------------------------
                */

                if (!empty($data['pickup_location'])) {

                    throw new ConflictException(
                        'Pickup location cannot be used for a self drive booking.'
                    );
                }

                if (!empty($data['drop_location'])) {

                    throw new ConflictException(
                        'Drop location cannot be used for a self drive booking.'
                    );
                }


                $pickupLocation = null;
                $dropLocation = null;
            }


            /*
            |--------------------------------------------------------------------------
            | WITH DRIVER
            |--------------------------------------------------------------------------
            |
            | Pickup = Customer location
            | Drop   = Customer location
            |
            */

            if (
                $rentalType ===
                Booking::RENTAL_TYPE_WITH_DRIVER
            ) {

                $pickupLocation =
                    $data['pickup_location']
                    ?? null;

                $dropLocation =
                    $data['drop_location']
                    ?? null;


                /*
                |--------------------------------------------------------------------------
                | Pickup location required
                |--------------------------------------------------------------------------
                */

                if (empty($pickupLocation)) {

                    throw new ConflictException(
                        'Pickup location is required for a driver booking.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Drop location required
                |--------------------------------------------------------------------------
                */

                if (empty($dropLocation)) {

                    throw new ConflictException(
                        'Drop location is required for a driver booking.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Branches are NOT allowed
                |--------------------------------------------------------------------------
                */

                if (!empty($data['pickup_branch_id'])) {

                    throw new ConflictException(
                        'Pickup branch cannot be used for a driver booking.'
                    );
                }

                if (!empty($data['drop_branch_id'])) {

                    throw new ConflictException(
                        'Drop branch cannot be used for a driver booking.'
                    );
                }


                $pickupBranchId = null;
                $dropBranchId = null;
            }


            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            $quotedAmount =
                $data['quoted_amount']
                ?? 0;

            $discountAmount =
                $data['discount_amount']
                ?? 0;


            /*
            |--------------------------------------------------------------------------
            | Validate quoted amount
            |--------------------------------------------------------------------------
            */

            if ((float) $quotedAmount < 0) {

                throw new ConflictException(
                    'Quoted amount cannot be negative.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Validate discount
            |--------------------------------------------------------------------------
            */

            if ((float) $discountAmount < 0) {

                throw new ConflictException(
                    'Discount amount cannot be negative.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Discount cannot exceed quoted amount
            |--------------------------------------------------------------------------
            */

            if (
                (float) $discountAmount >
                (float) $quotedAmount
            ) {

                throw new ConflictException(
                    'Discount cannot be greater than quoted amount.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate final amount
            |--------------------------------------------------------------------------
            */

            $finalAmount = max(
                (float) $quotedAmount -
                (float) $discountAmount,
                0
            );


            /*
            |--------------------------------------------------------------------------
            | Create booking
            |--------------------------------------------------------------------------
            */

            return $this->repository->create([

                'slug' =>
                    (string) Str::uuid(),

                'customer_id' =>
                    $data['customer_id'],

                'vehicle_id' =>
                    $data['vehicle_id'],

                'rental_type' =>
                    $rentalType,


                /*
                |--------------------------------------------------------------------------
                | Branches
                |--------------------------------------------------------------------------
                */

                'pickup_branch_id' =>
                    $pickupBranchId,

                'drop_branch_id' =>
                    $dropBranchId,


                /*
                |--------------------------------------------------------------------------
                | Locations
                |--------------------------------------------------------------------------
                */

                'pickup_location' =>
                    $pickupLocation,

                'drop_location' =>
                    $dropLocation,


                /*
                |--------------------------------------------------------------------------
                | Dates
                |--------------------------------------------------------------------------
                */

                'pickup_at' =>
                    $data['pickup_at'],

                'expected_return_at' =>
                    $data['expected_return_at'],


                /*
                |--------------------------------------------------------------------------
                | Amounts
                |--------------------------------------------------------------------------
                */

                'quoted_amount' =>
                    $quotedAmount,

                'discount_amount' =>
                    $discountAmount,

                'final_amount' =>
                    $finalAmount,


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                'status' =>
                    Booking::STATUS_PENDING,


                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                'customer_notes' =>
                    $data['customer_notes']
                    ?? null,

                'admin_notes' =>
                    $data['admin_notes']
                    ?? null,
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Approve booking
    |--------------------------------------------------------------------------
    */

    public function approve(
        Booking $booking,
        int $userId
    ) {
        if (!$booking->canBeApproved()) {

            throw new ConflictException(
                'Only pending booking can be approved.'
            );
        }

        return $this->repository->update(
            $booking,
            [
                'status' =>
                    Booking::STATUS_APPROVED,

                'approved_by' =>
                    $userId,

                'approved_at' =>
                    now(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject booking
    |--------------------------------------------------------------------------
    */

    public function reject(
        Booking $booking
    ) {
        if (!$booking->isPending()) {

            throw new ConflictException(
                'Only pending booking can be rejected.'
            );
        }

        return $this->repository->update(
            $booking,
            [
                'status' =>
                    Booking::STATUS_REJECTED,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel booking
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Booking $booking
    ) {
        if (!$booking->canBeCancelled()) {

            throw new ConflictException(
                'Booking cannot be cancelled.'
            );
        }

        return $this->repository->update(
            $booking,
            [
                'status' =>
                    Booking::STATUS_CANCELLED,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Convert booking into trip
    |--------------------------------------------------------------------------
    */

    public function createTrip(
        Booking $booking
    ): Booking {

        return DB::transaction(function () use ($booking) {

            /*
            |--------------------------------------------------------------------------
            | Booking must be approved
            |--------------------------------------------------------------------------
            */

            if (
                $booking->status !==
                Booking::STATUS_APPROVED
            ) {

                throw new ConflictException(
                    'Only approved booking can create trip.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate trip
            |--------------------------------------------------------------------------
            */

            if ($booking->trip_id) {

                throw new ConflictException(
                    'A trip has already been created for this booking.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Load relationships
            |--------------------------------------------------------------------------
            */

            $booking->load([
                'vehicle',
                'customer',
                'pickupBranch',
                'dropBranch',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Basic trip data
            |--------------------------------------------------------------------------
            */

            $tripData = [

                'slug' =>
                    (string) Str::uuid(),

                'customer_id' =>
                    $booking->customer_id,

                'vehicle_id' =>
                    $booking->vehicle_id,

                'rental_type' =>
                    $booking->rental_type,

                'pickup_at' =>
                    $booking->pickup_at,

                'expected_return_at' =>
                    $booking->expected_return_at,

                'pickup_odometer' =>
                    0,

                'pickup_fuel' =>
                    'full',

                'base_amount' =>
                    $booking->final_amount,

                'total_amount' =>
                    $booking->final_amount,

                'status' =>
                    Trip::STATUS_SCHEDULED,
            ];


            /*
            |--------------------------------------------------------------------------
            | SELF DRIVE
            |--------------------------------------------------------------------------
            */

            if (
                $booking->rental_type ===
                Booking::RENTAL_TYPE_SELF_DRIVE
            ) {

                if (!$booking->pickup_branch_id) {

                    throw new ConflictException(
                        'Pickup branch is required for a self drive trip.'
                    );
                }

                if (!$booking->drop_branch_id) {

                    throw new ConflictException(
                        'Drop branch is required for a self drive trip.'
                    );
                }


                $tripData['pickup_branch_id'] =
                    $booking->pickup_branch_id;

                $tripData['drop_branch_id'] =
                    $booking->drop_branch_id;

                $tripData['pickup_location'] =
                    null;

                $tripData['drop_location'] =
                    null;

                /*
                | Self drive has no driver
                */

                $tripData['driver_id'] =
                    null;
            }


            /*
            |--------------------------------------------------------------------------
            | WITH DRIVER
            |--------------------------------------------------------------------------
            */

            if (
                $booking->rental_type ===
                Booking::RENTAL_TYPE_WITH_DRIVER
            ) {

                if (
                    empty($booking->pickup_location)
                    ||
                    empty($booking->drop_location)
                ) {

                    throw new ConflictException(
                        'Pickup and drop locations are required for a driver trip.'
                    );
                }


                /*
                | No branches for With Driver
                */

                $tripData['pickup_branch_id'] =
                    null;

                $tripData['drop_branch_id'] =
                    null;

                $tripData['pickup_location'] =
                    $booking->pickup_location;

                $tripData['drop_location'] =
                    $booking->drop_location;

                /*
                | Driver can be assigned later
                */

                $tripData['driver_id'] =
                    null;
            }


            /*
            |--------------------------------------------------------------------------
            | Create Trip
            |--------------------------------------------------------------------------
            */

            $trip = $this->tripService->create(
                $tripData
            );


            /*
            |--------------------------------------------------------------------------
            | Link Trip to Booking
            |--------------------------------------------------------------------------
            */

            $booking->update([

                'trip_id' =>
                    $trip->id,

                'status' =>
                    Booking::STATUS_TRIP_CREATED,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Return updated booking
            |--------------------------------------------------------------------------
            */

            return $booking->fresh([
                'trip',
                'vehicle',
                'customer',
                'pickupBranch',
                'dropBranch',
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Restore booking
    |--------------------------------------------------------------------------
    */

    public function restore(
        Booking $booking
    ) {
        if (!$booking->canBeRestored()) {

            throw new ConflictException(
                'Only cancelled or rejected booking can be restored.'
            );
        }

        return $this->repository->update(
            $booking,
            [
                'status' =>
                    Booking::STATUS_PENDING,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update booking
    |--------------------------------------------------------------------------
    */

    public function update(
        Booking $booking,
        array $data
    ) {

        if (!$booking->canBeUpdated()) {

            throw new ConflictException(
                'Only pending booking can be updated.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Rental type
        |--------------------------------------------------------------------------
        */

        $rentalType =
            $data['rental_type']
            ?? $booking->rental_type;


        /*
        |--------------------------------------------------------------------------
        | Validate rental type
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $rentalType,
            [
                Booking::RENTAL_TYPE_SELF_DRIVE,
                Booking::RENTAL_TYPE_WITH_DRIVER,
            ],
            true
        )) {

            throw new ConflictException(
                'Invalid rental type.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Normalize
        |--------------------------------------------------------------------------
        */

        $pickupBranchId = null;
        $dropBranchId = null;

        $pickupLocation = null;
        $dropLocation = null;


        /*
        |--------------------------------------------------------------------------
        | SELF DRIVE
        |--------------------------------------------------------------------------
        */

        if (
            $rentalType ===
            Booking::RENTAL_TYPE_SELF_DRIVE
        ) {

            $pickupBranchId =
                array_key_exists(
                    'pickup_branch_id',
                    $data
                )
                    ? $data['pickup_branch_id']
                    : $booking->pickup_branch_id;

            $dropBranchId =
                array_key_exists(
                    'drop_branch_id',
                    $data
                )
                    ? $data['drop_branch_id']
                    : $booking->drop_branch_id;


            if (!$pickupBranchId) {

                throw new ConflictException(
                    'Pickup branch is required for a self drive booking.'
                );
            }


            if (!$dropBranchId) {

                throw new ConflictException(
                    'Drop branch is required for a self drive booking.'
                );
            }


            if (
                array_key_exists(
                    'pickup_location',
                    $data
                )
                &&
                !empty($data['pickup_location'])
            ) {

                throw new ConflictException(
                    'Pickup location cannot be used for a self drive booking.'
                );
            }


            if (
                array_key_exists(
                    'drop_location',
                    $data
                )
                &&
                !empty($data['drop_location'])
            ) {

                throw new ConflictException(
                    'Drop location cannot be used for a self drive booking.'
                );
            }


            $pickupLocation = null;
            $dropLocation = null;
        }


        /*
        |--------------------------------------------------------------------------
        | WITH DRIVER
        |--------------------------------------------------------------------------
        */

        if (
            $rentalType ===
            Booking::RENTAL_TYPE_WITH_DRIVER
        ) {

            $pickupLocation =
                array_key_exists(
                    'pickup_location',
                    $data
                )
                    ? $data['pickup_location']
                    : $booking->pickup_location;

            $dropLocation =
                array_key_exists(
                    'drop_location',
                    $data
                )
                    ? $data['drop_location']
                    : $booking->drop_location;


            if (empty($pickupLocation)) {

                throw new ConflictException(
                    'Pickup location is required for a driver booking.'
                );
            }


            if (empty($dropLocation)) {

                throw new ConflictException(
                    'Drop location is required for a driver booking.'
                );
            }


            if (
                array_key_exists(
                    'pickup_branch_id',
                    $data
                )
                &&
                !empty($data['pickup_branch_id'])
            ) {

                throw new ConflictException(
                    'Pickup branch cannot be used for a driver booking.'
                );
            }


            if (
                array_key_exists(
                    'drop_branch_id',
                    $data
                )
                &&
                !empty($data['drop_branch_id'])
            ) {

                throw new ConflictException(
                    'Drop branch cannot be used for a driver booking.'
                );
            }


            $pickupBranchId = null;
            $dropBranchId = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Amounts
        |--------------------------------------------------------------------------
        */

        $quotedAmount =
            $data['quoted_amount']
            ?? $booking->quoted_amount;

        $discountAmount =
            $data['discount_amount']
            ?? $booking->discount_amount;


        if ((float) $quotedAmount < 0) {

            throw new ConflictException(
                'Quoted amount cannot be negative.'
            );
        }


        if ((float) $discountAmount < 0) {

            throw new ConflictException(
                'Discount amount cannot be negative.'
            );
        }


        if (
            (float) $discountAmount >
            (float) $quotedAmount
        ) {

            throw new ConflictException(
                'Discount cannot be greater than quoted amount.'
            );
        }


        $finalAmount = max(
            (float) $quotedAmount -
            (float) $discountAmount,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        return $this->repository->update(
            $booking,
            [

                'customer_id' =>
                    $data['customer_id']
                    ?? $booking->customer_id,

                'vehicle_id' =>
                    $data['vehicle_id']
                    ?? $booking->vehicle_id,

                'rental_type' =>
                    $rentalType,


                /*
                | Branch
                */

                'pickup_branch_id' =>
                    $pickupBranchId,

                'drop_branch_id' =>
                    $dropBranchId,


                /*
                | Location
                */

                'pickup_location' =>
                    $pickupLocation,

                'drop_location' =>
                    $dropLocation,


                /*
                | Dates
                */

                'pickup_at' =>
                    $data['pickup_at']
                    ?? $booking->pickup_at,

                'expected_return_at' =>
                    $data['expected_return_at']
                    ?? $booking->expected_return_at,


                /*
                | Amount
                */

                'quoted_amount' =>
                    $quotedAmount,

                'discount_amount' =>
                    $discountAmount,

                'final_amount' =>
                    $finalAmount,


                /*
                | Notes
                */

                'customer_notes' =>
                    $data['customer_notes']
                    ?? $booking->customer_notes,

                'admin_notes' =>
                    $data['admin_notes']
                    ?? $booking->admin_notes,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete booking
    |--------------------------------------------------------------------------
    */

    public function delete(
        Booking $booking
    ) {

        if (!$booking->canBeDeleted()) {

            throw new ConflictException(
                'Booking cannot be deleted in its current status.'
            );
        }

        return $this->repository->delete(
            $booking
        );
    }
}