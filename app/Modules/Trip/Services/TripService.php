<?php

namespace App\Modules\Trip\Services;

use Illuminate\Support\Facades\DB;

use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;

use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;

class TripService
{
    public function __construct(
        protected TripRepositoryInterface $repository
    ) {}


    /*
    |--------------------------------------------------------------------------
    | List Trips
    |--------------------------------------------------------------------------
    */

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Trip
    |--------------------------------------------------------------------------
    */

    public function getById(int $id): Trip
    {
        $trip = $this->repository->getById($id);

        if (!$trip) {
            throw new NotFoundException(
                'Trip not found.'
            );
        }

        return $trip;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Trip
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Trip
    {
        return DB::transaction(function () use ($data) {

            /*
            |------------------------------------------------------------------
            | Rental Type
            |------------------------------------------------------------------
            */

            $rentalType =
                $data['rental_type']
                ?? Trip::RENTAL_TYPE_SELF_DRIVE;


            /*
            |------------------------------------------------------------------
            | Validate Rental Type
            |------------------------------------------------------------------
            */

            if (!in_array(
                $rentalType,
                [
                    Trip::RENTAL_TYPE_SELF_DRIVE,
                    Trip::RENTAL_TYPE_WITH_DRIVER,
                ],
                true
            )) {
                throw new ConflictException(
                    'Invalid rental type.'
                );
            }


            /*
            |------------------------------------------------------------------
            | Vehicle Availability
            |------------------------------------------------------------------
            */

            if (
                $this->repository->hasActiveTripForVehicle(
                    (int) $data['vehicle_id']
                )
            ) {
                throw new ConflictException(
                    'Vehicle already has an active trip.'
                );
            }


            /*
            |------------------------------------------------------------------
            | Pickup Odometer
            |------------------------------------------------------------------
            */

            $pickupOdometer =
                $data['pickup_odometer']
                ?? 0;

            if ((float) $pickupOdometer < 0) {

                throw new ConflictException(
                    'Invalid pickup odometer.'
                );
            }


            /*
            |------------------------------------------------------------------
            | Normalize Fields
            |------------------------------------------------------------------
            */

            $pickupBranchId = null;
            $dropBranchId = null;

            $pickupLocation = null;
            $dropLocation = null;


            /*
            |==================================================================
            | SELF DRIVE
            |==================================================================
            |
            | Pickup branch = REQUIRED
            | Drop branch   = OPTIONAL
            | Locations     = NULL
            |
            */

            if (
                $rentalType ===
                Trip::RENTAL_TYPE_SELF_DRIVE
            ) {

                $pickupBranchId =
                    $data['pickup_branch_id']
                    ?? null;

                $dropBranchId =
                    $data['drop_branch_id']
                    ?? null;


                /*
                |--------------------------------------------------------------
                | Pickup branch required
                |--------------------------------------------------------------
                */

                if (!$pickupBranchId) {

                    throw new ConflictException(
                        'Pickup branch is required for a self drive trip.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Locations cannot be used
                |--------------------------------------------------------------
                */

                if (!empty($data['pickup_location'])) {

                    throw new ConflictException(
                        'Pickup location cannot be used for a self drive trip.'
                    );
                }

                if (!empty($data['drop_location'])) {

                    throw new ConflictException(
                        'Drop location cannot be used for a self drive trip.'
                    );
                }
            }


            /*
            |==================================================================
            | WITH DRIVER
            |==================================================================
            |
            | Pickup branch   = REQUIRED
            | Drop branch     = OPTIONAL
            | Pickup location = REQUIRED
            | Drop location   = REQUIRED
            |
            */

            if (
                $rentalType ===
                Trip::RENTAL_TYPE_WITH_DRIVER
            ) {

                $pickupBranchId =
                    $data['pickup_branch_id']
                    ?? null;

                $dropBranchId =
                    $data['drop_branch_id']
                    ?? null;

                $pickupLocation =
                    $data['pickup_location']
                    ?? null;

                $dropLocation =
                    $data['drop_location']
                    ?? null;


                /*
                |--------------------------------------------------------------
                | Pickup branch required
                |--------------------------------------------------------------
                */

                if (!$pickupBranchId) {

                    throw new ConflictException(
                        'Pickup branch is required for a driver trip.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Pickup location required
                |--------------------------------------------------------------
                */

                if (empty($pickupLocation)) {

                    throw new ConflictException(
                        'Pickup location is required for a driver trip.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Drop location required
                |--------------------------------------------------------------
                */

                if (empty($dropLocation)) {

                    throw new ConflictException(
                        'Drop location is required for a driver trip.'
                    );
                }
            }


            /*
            |------------------------------------------------------------------
            | Amount
            |------------------------------------------------------------------
            */

            $baseAmount =
                $data['base_amount']
                ?? 0;

            if ((float) $baseAmount < 0) {

                throw new ConflictException(
                    'Base amount cannot be negative.'
                );
            }


            $totalAmount =
                $data['total_amount']
                ?? $baseAmount;


            if ((float) $totalAmount < 0) {

                throw new ConflictException(
                    'Total amount cannot be negative.'
                );
            }


            /*
            |------------------------------------------------------------------
            | Prepare Data
            |------------------------------------------------------------------
            */

            $data['rental_type'] =
                $rentalType;

            $data['pickup_branch_id'] =
                $pickupBranchId;

            $data['drop_branch_id'] =
                $dropBranchId;

            $data['pickup_location'] =
                $pickupLocation;

            $data['drop_location'] =
                $dropLocation;

            $data['pickup_odometer'] =
                $pickupOdometer;

            $data['base_amount'] =
                $baseAmount;

            $data['total_amount'] =
                $totalAmount;


            /*
            |------------------------------------------------------------------
            | Create
            |------------------------------------------------------------------
            */

            return $this->repository->create(
                $data
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Update Trip
    |--------------------------------------------------------------------------
    */

    public function update(
        Trip $trip,
        array $data
    ): Trip {

        return DB::transaction(function () use ($trip, $data) {

            /*
            |------------------------------------------------------------------
            | Vehicle Conflict
            |------------------------------------------------------------------
            */

            if (
                isset($data['vehicle_id']) &&
                (int) $data['vehicle_id'] !==
                (int) $trip->vehicle_id
            ) {

                if (
                    $this->repository->hasActiveTripForVehicle(
                        (int) $data['vehicle_id'],
                        $trip->id
                    )
                ) {
                    throw new ConflictException(
                        'Vehicle already has an active trip.'
                    );
                }
            }


            /*
            |------------------------------------------------------------------
            | Return Odometer
            |------------------------------------------------------------------
            */

            if (
                isset($data['return_odometer']) &&
                $data['return_odometer'] !== null &&
                $data['return_odometer'] <
                $trip->pickup_odometer
            ) {

                throw new ConflictException(
                    'Return odometer cannot be less than pickup odometer.'
                );
            }


            /*
            |------------------------------------------------------------------
            | Rental Type
            |------------------------------------------------------------------
            */

            $rentalType =
                $data['rental_type']
                ?? $trip->rental_type;


            /*
            |------------------------------------------------------------------
            | Validate Rental Type
            |------------------------------------------------------------------
            */

            if (!in_array(
                $rentalType,
                [
                    Trip::RENTAL_TYPE_SELF_DRIVE,
                    Trip::RENTAL_TYPE_WITH_DRIVER,
                ],
                true
            )) {

                throw new ConflictException(
                    'Invalid rental type.'
                );
            }


            /*
            |==================================================================
            | SELF DRIVE
            |==================================================================
            */

            if (
                $rentalType ===
                Trip::RENTAL_TYPE_SELF_DRIVE
            ) {

                /*
                |--------------------------------------------------------------
                | Pickup branch
                |--------------------------------------------------------------
                */

                $data['pickup_branch_id'] =
                    array_key_exists(
                        'pickup_branch_id',
                        $data
                    )
                        ? $data['pickup_branch_id']
                        : $trip->pickup_branch_id;


                /*
                |--------------------------------------------------------------
                | Drop branch
                |--------------------------------------------------------------
                */

                $data['drop_branch_id'] =
                    array_key_exists(
                        'drop_branch_id',
                        $data
                    )
                        ? $data['drop_branch_id']
                        : $trip->drop_branch_id;


                /*
                |--------------------------------------------------------------
                | Pickup branch required
                |--------------------------------------------------------------
                */

                if (!$data['pickup_branch_id']) {

                    throw new ConflictException(
                        'Pickup branch is required for a self drive trip.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Locations must be NULL
                |--------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'pickup_location',
                        $data
                    ) &&
                    !empty($data['pickup_location'])
                ) {

                    throw new ConflictException(
                        'Pickup location cannot be used for a self drive trip.'
                    );
                }


                if (
                    array_key_exists(
                        'drop_location',
                        $data
                    ) &&
                    !empty($data['drop_location'])
                ) {

                    throw new ConflictException(
                        'Drop location cannot be used for a self drive trip.'
                    );
                }


                $data['pickup_location'] =
                    null;

                $data['drop_location'] =
                    null;


                /*
                |--------------------------------------------------------------
                | Self drive does not use driver
                |--------------------------------------------------------------
                */

                $data['driver_id'] =
                    null;
            }


            /*
            |==================================================================
            | WITH DRIVER
            |==================================================================
            */

            if (
                $rentalType ===
                Trip::RENTAL_TYPE_WITH_DRIVER
            ) {

                /*
                |--------------------------------------------------------------
                | Pickup branch
                |--------------------------------------------------------------
                */

                $data['pickup_branch_id'] =
                    array_key_exists(
                        'pickup_branch_id',
                        $data
                    )
                        ? $data['pickup_branch_id']
                        : $trip->pickup_branch_id;


                /*
                |--------------------------------------------------------------
                | Drop branch
                |--------------------------------------------------------------
                */

                $data['drop_branch_id'] =
                    array_key_exists(
                        'drop_branch_id',
                        $data
                    )
                        ? $data['drop_branch_id']
                        : $trip->drop_branch_id;


                /*
                |--------------------------------------------------------------
                | Pickup location
                |--------------------------------------------------------------
                */

                $data['pickup_location'] =
                    array_key_exists(
                        'pickup_location',
                        $data
                    )
                        ? $data['pickup_location']
                        : $trip->pickup_location;


                /*
                |--------------------------------------------------------------
                | Drop location
                |--------------------------------------------------------------
                */

                $data['drop_location'] =
                    array_key_exists(
                        'drop_location',
                        $data
                    )
                        ? $data['drop_location']
                        : $trip->drop_location;


                /*
                |--------------------------------------------------------------
                | Pickup branch required
                |--------------------------------------------------------------
                */

                if (!$data['pickup_branch_id']) {

                    throw new ConflictException(
                        'Pickup branch is required for a driver trip.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Pickup location required
                |--------------------------------------------------------------
                */

                if (empty($data['pickup_location'])) {

                    throw new ConflictException(
                        'Pickup location is required for a driver trip.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Drop location required
                |--------------------------------------------------------------
                */

                if (empty($data['drop_location'])) {

                    throw new ConflictException(
                        'Drop location is required for a driver trip.'
                    );
                }
            }


            /*
            |------------------------------------------------------------------
            | Rental Type
            |------------------------------------------------------------------
            */

            $data['rental_type'] =
                $rentalType;


            /*
            |------------------------------------------------------------------
            | Update
            |------------------------------------------------------------------
            */

            return $this->repository->update(
                $trip,
                $data
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Pickup Trip
    |--------------------------------------------------------------------------
    */

    public function pickup(
        Trip $trip,
        int $staffId
    ): Trip {

        return DB::transaction(function () use (
            $trip,
            $staffId
        ) {

            if (!$trip->isScheduled()) {

                throw new ConflictException(
                    'Only scheduled trips can be picked up.'
                );
            }


            return $this->repository->update(
                $trip,
                [
                    'pickup_staff_id' =>
                        $staffId,

                    'status' =>
                        Trip::STATUS_PICKED_UP,
                ]
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Start Trip
    |--------------------------------------------------------------------------
    */

    public function start(
        Trip $trip
    ): Trip {

        return DB::transaction(function () use ($trip) {

            if (
                $trip->status !==
                Trip::STATUS_PICKED_UP
            ) {

                throw new ConflictException(
                    'Trip must be picked up first.'
                );
            }


            return $this->repository->update(
                $trip,
                [
                    'status' =>
                        Trip::STATUS_ON_TRIP,
                ]
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Complete Trip
    |--------------------------------------------------------------------------
    */

    public function complete(
        Trip $trip,
        array $data,
        int $staffId
    ): Trip {

        return DB::transaction(
            function () use (
                $trip,
                $data,
                $staffId
            ) {

                if (!$trip->isActive()) {

                    throw new ConflictException(
                        'Only active trips can be completed.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Return Odometer
                |--------------------------------------------------------------
                */

                $returnOdometer =
                    $data['return_odometer'];


                if (
                    $returnOdometer <
                    $trip->pickup_odometer
                ) {

                    throw new ConflictException(
                        'Return odometer is invalid.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Charges
                |--------------------------------------------------------------
                */

                $extraKmCharge =
                    $data['extra_km_charge']
                    ?? 0;

                $lateCharge =
                    $data['late_return_charge']
                    ?? 0;

                $damageCharge =
                    $data['damage_charge']
                    ?? 0;

                $fuelCharge =
                    $data['fuel_charge']
                    ?? 0;


                /*
                |--------------------------------------------------------------
                | Validate Charges
                |--------------------------------------------------------------
                */

                if (
                    (float) $extraKmCharge < 0 ||
                    (float) $lateCharge < 0 ||
                    (float) $damageCharge < 0 ||
                    (float) $fuelCharge < 0
                ) {

                    throw new ConflictException(
                        'Trip charges cannot be negative.'
                    );
                }


                /*
                |--------------------------------------------------------------
                | Total
                |--------------------------------------------------------------
                */

                $total =
                    (float) $trip->base_amount
                    + (float) $extraKmCharge
                    + (float) $lateCharge
                    + (float) $damageCharge
                    + (float) $fuelCharge;


                /*
                |--------------------------------------------------------------
                | Complete
                |--------------------------------------------------------------
                */

                return $this->repository->update(
                    $trip,
                    [

                        'actual_return_at' =>
                            $data['actual_return_at'],

                        'return_odometer' =>
                            $returnOdometer,

                        'return_fuel' =>
                            $data['return_fuel'],

                        'return_notes' =>
                            $data['return_notes']
                            ?? null,

                        'damage_notes' =>
                            $data['damage_notes']
                            ?? null,

                        'extra_km_charge' =>
                            $extraKmCharge,

                        'late_return_charge' =>
                            $lateCharge,

                        'damage_charge' =>
                            $damageCharge,

                        'fuel_charge' =>
                            $fuelCharge,

                        'total_amount' =>
                            $total,

                        'return_staff_id' =>
                            $staffId,

                        'status' =>
                            Trip::STATUS_COMPLETED,
                    ]
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Trip
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Trip $trip,
        string $reason,
        int $userId
    ): Trip {

        return DB::transaction(
            function () use (
                $trip,
                $reason,
                $userId
            ) {

                if (!$trip->canBeCancelled()) {

                    throw new ConflictException(
                        'Only scheduled trips can be cancelled.'
                    );
                }


                return $this->repository->update(
                    $trip,
                    [

                        'status' =>
                            Trip::STATUS_CANCELLED,

                        'cancelled_by' =>
                            $userId,

                        'cancellation_reason' =>
                            $reason,

                        'cancelled_at' =>
                            now(),
                    ]
                );
            }
        );
    }
}