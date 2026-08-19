<?php

namespace App\Modules\Trip\Repositories;

use Illuminate\Support\Str;

use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;

class TripRepository implements TripRepositoryInterface
{
    /**
     * Common relationships used throughout Trip.
     */
    protected array $relations = [
        'customer',
        'vehicle',
        'driver',
        'pickupBranch',
        'dropBranch',
        'pickupStaff',
        'returnStaff',
        'cancelledBy',
        'booking',
        'payments',
    ];

    /**
     * List trips.
     */
    public function getAll(array $filters = [])
    {
        return Trip::query()
            ->with($this->relations)
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    /**
     * Get trip by ID.
     */
    public function getById(int $id): ?Trip
    {
        return Trip::with($this->relations)
            ->find($id);
    }

    /**
     * Get trip by slug.
     */
    public function findBySlug(string $slug): ?Trip
    {
        return Trip::with($this->relations)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create trip.
     */
    public function create(array $data): Trip
    {
        $trip = Trip::create([

            'slug' => $data['slug'] ?? (string) Str::uuid(),

            /*
            |----------------------------------------------------------------------
            | Customer / Vehicle
            |----------------------------------------------------------------------
            */

            'customer_id' => $data['customer_id'],
            'vehicle_id' => $data['vehicle_id'],

            /*
            |----------------------------------------------------------------------
            | Rental
            |----------------------------------------------------------------------
            */

            'rental_type' =>
                $data['rental_type']
                ?? Trip::RENTAL_TYPE_SELF_DRIVE,

            'driver_id' =>
                $data['driver_id']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Staff
            |----------------------------------------------------------------------
            */

            'pickup_staff_id' =>
                $data['pickup_staff_id']
                ?? null,

            'return_staff_id' =>
                $data['return_staff_id']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Branch
            |----------------------------------------------------------------------
            */

            'pickup_branch_id' =>
                $data['pickup_branch_id']
                ?? null,

            'drop_branch_id' =>
                $data['drop_branch_id']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Location
            |----------------------------------------------------------------------
            */

            'pickup_location' =>
                $data['pickup_location']
                ?? null,

            'drop_location' =>
                $data['drop_location']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Schedule
            |----------------------------------------------------------------------
            */

            'pickup_at' =>
                $data['pickup_at'],

            'expected_return_at' =>
                $data['expected_return_at'],

            'actual_return_at' =>
                $data['actual_return_at']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Odometer
            |----------------------------------------------------------------------
            */

            'pickup_odometer' =>
                $data['pickup_odometer'],

            'return_odometer' =>
                $data['return_odometer']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Fuel
            |----------------------------------------------------------------------
            */

            'pickup_fuel' =>
                $data['pickup_fuel']
                ?? null,

            'return_fuel' =>
                $data['return_fuel']
                ?? null,

            /*
            |----------------------------------------------------------------------
            | Billing
            |----------------------------------------------------------------------
            */

            'base_amount' =>
                $data['base_amount']
                ?? 0,

            'extra_km_charge' =>
                $data['extra_km_charge']
                ?? 0,

            'late_return_charge' =>
                $data['late_return_charge']
                ?? 0,

            'damage_charge' =>
                $data['damage_charge']
                ?? 0,

            'fuel_charge' =>
                $data['fuel_charge']
                ?? 0,

            'total_amount' =>
                $data['total_amount']
                ?? 0,

            /*
            |----------------------------------------------------------------------
            | Status
            |----------------------------------------------------------------------
            */

            'status' =>
                $data['status']
                ?? Trip::STATUS_SCHEDULED,

            /*
            |----------------------------------------------------------------------
            | Notes
            |----------------------------------------------------------------------
            */

            'pickup_notes' =>
                $data['pickup_notes']
                ?? null,

            'return_notes' =>
                $data['return_notes']
                ?? null,

            'damage_notes' =>
                $data['damage_notes']
                ?? null,

        ]);

        return $trip->load($this->relations);
    }

    /**
     * Update trip.
     */
    public function update(
        Trip $trip,
        array $data
    ): Trip {

        $trip->update($data);

        return $trip->fresh($this->relations);
    }

    /**
     * Check if vehicle has an active trip.
     *
     * Active:
     * - picked_up
     * - on_trip
     */
    public function hasActiveTripForVehicle(
        int $vehicleId,
        ?int $ignoreTripId = null
    ): bool {

        return Trip::query()
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', [
                Trip::STATUS_PICKED_UP,
                Trip::STATUS_ON_TRIP,
            ])
            ->when(
                $ignoreTripId !== null,
                fn ($query) =>
                    $query->where('id', '!=', $ignoreTripId)
            )
            ->exists();
    }
}