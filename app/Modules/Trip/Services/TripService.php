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

    /**
     * List trips.
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get trip by ID.
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

    /**
     * Create trip.
     */
    public function create(array $data): Trip
    {
        return DB::transaction(function () use ($data) {

            // Vehicle availability
            if (
                $this->repository->hasActiveTripForVehicle(
                    $data['vehicle_id']
                )
            ) {
                throw new ConflictException(
                    'Vehicle already has an active trip.'
                );
            }

            // Odometer validation
            if ($data['pickup_odometer'] < 0) {
                throw new ConflictException(
                    'Invalid pickup odometer.'
                );
            }

            // Initial amount
            $data['total_amount'] =
                $data['base_amount'] ?? 0;

            return $this->repository->create($data);

        });
    }

    /**
     * Update trip.
     */
    public function update(
        Trip $trip,
        array $data
    ): Trip {

        return DB::transaction(function () use ($trip, $data) {

            // Prevent vehicle conflict
            if (
                isset($data['vehicle_id']) &&
                $this->repository->hasActiveTripForVehicle(
                    $data['vehicle_id'],
                    $trip->id
                )
            ) {
                throw new ConflictException(
                    'Vehicle already has an active trip.'
                );
            }

            // Return odometer validation
            if (
                isset($data['return_odometer']) &&
                $data['return_odometer'] < $trip->pickup_odometer
            ) {
                throw new ConflictException(
                    'Return odometer cannot be less than pickup odometer.'
                );
            }

            return $this->repository->update(
                $trip,
                $data
            );

        });
    }

    /**
     * Pickup trip.
     */
    public function pickup(
        Trip $trip,
        int $staffId
    ): Trip {

        return DB::transaction(function () use ($trip, $staffId) {

            if ($trip->status !== 'scheduled') {
                throw new ConflictException(
                    'Only scheduled trips can be picked up.'
                );
            }

            return $this->repository->update($trip, [

                'pickup_staff_id' => $staffId,

                'status' => 'picked_up',

            ]);

        });
    }

    /**
     * Start trip.
     */
    public function start(Trip $trip): Trip
    {
        return DB::transaction(function () use ($trip) {

            if ($trip->status !== 'picked_up') {
                throw new ConflictException(
                    'Trip must be picked up first.'
                );
            }

            return $this->repository->update($trip, [

                'status' => 'on_trip',

            ]);

        });
    }

    /**
     * Complete trip.
     */
    public function complete(
        Trip $trip,
        array $data,
        int $staffId
    ): Trip {

        return DB::transaction(function () use ($trip, $data, $staffId) {

            if (!$trip->isActive()) {
                throw new ConflictException(
                    'Only active trips can be completed.'
                );
            }

            // Validate odometer
            if (
                $data['return_odometer'] < $trip->pickup_odometer
            ) {
                throw new ConflictException(
                    'Return odometer is invalid.'
                );
            }

            // Calculate distance
            $distance =
                $data['return_odometer']
                - $trip->pickup_odometer;

            // Example charge calculation
            $extraKmCharge = $data['extra_km_charge'] ?? 0;
            $lateCharge = $data['late_return_charge'] ?? 0;
            $damageCharge = $data['damage_charge'] ?? 0;
            $fuelCharge = $data['fuel_charge'] ?? 0;

            $total =
                $trip->base_amount
                + $extraKmCharge
                + $lateCharge
                + $damageCharge
                + $fuelCharge;

            return $this->repository->update($trip, [

                'actual_return_at' => $data['actual_return_at'],
                'return_odometer' => $data['return_odometer'],
                'return_fuel' => $data['return_fuel'],
                'return_notes' => $data['return_notes'] ?? null,
                'damage_notes' => $data['damage_notes'] ?? null,
                'extra_km_charge' => $extraKmCharge,
                'late_return_charge' => $lateCharge,
                'damage_charge' => $damageCharge,
                'fuel_charge' => $fuelCharge,
                'total_amount' => $total,
                'return_staff_id' => $staffId,
                'status' => 'completed',

            ]);

        });
    }

    
    /**
     * Cancel trip.
     */
    public function cancel(
        Trip $trip,
        string $reason,
        int $userId
    ): Trip {

        return DB::transaction(function () use ($trip, $reason, $userId) {

            // Only scheduled trips can be cancelled
            if ($trip->status !== 'scheduled') {
                throw new ConflictException(
                    'Only scheduled trips can be cancelled.'
                );
            }

            return $this->repository->update($trip, [

                'status' => 'cancelled',

                'cancelled_by' => $userId,

                'cancellation_reason' => $reason,

                'cancelled_at' => now(),

            ]);

        });
    }
}