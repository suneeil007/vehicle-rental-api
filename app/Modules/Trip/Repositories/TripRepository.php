<?php

namespace App\Modules\Trip\Repositories;

use Illuminate\Support\Str;

use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;

class TripRepository implements TripRepositoryInterface
{
    /**
     * List trips.
     */
    public function getAll(array $filters = [])
    {
        return Trip::query()
            ->with([
                'customer',
                'vehicle',
                'driver',
                'pickupBranch',
                'dropBranch',
                'pickupStaff',
                'returnStaff',
                'cancelledBy',
            ])
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    /**
     * Get trip by ID.
     */
    public function getById(
        int $id
    ): ?Trip {

        return Trip::with([
                'customer',
                'vehicle',
                'driver',
                'pickupBranch',
                'dropBranch',
                'pickupStaff',
                'returnStaff',
                'cancelledBy',
            ])
            ->find($id);
    }


    public function findBySlug(string $slug): ?Trip
    {
        return Trip::with([
            'customer',
            'vehicle',
            'driver',
            'pickupBranch',
            'dropBranch',
            'pickupStaff',
            'returnStaff',
            'cancelledBy',
        ])->where('slug', $slug)->first();
    }

    /**
     * Create trip.
     */
    public function create(array $data): Trip
    {
        $trip = Trip::create([

            'slug' => (string) Str::uuid(),
            'customer_id' => $data['customer_id'],
            'vehicle_id' => $data['vehicle_id'],
            'rental_type' => $data['rental_type'] ?? 'self_drive',
            'driver_id' => $data['driver_id'] ?? null,
            'pickup_staff_id' => $data['pickup_staff_id'] ?? null,
            'return_staff_id' => $data['return_staff_id'] ?? null,
            'pickup_branch_id' => $data['pickup_branch_id'],
            'drop_branch_id' => $data['drop_branch_id'] ?? null,
            'pickup_at' => $data['pickup_at'],
            'expected_return_at' => $data['expected_return_at'],
            'actual_return_at' => $data['actual_return_at'] ?? null,
            'pickup_odometer' => $data['pickup_odometer'],
            'return_odometer' => $data['return_odometer'] ?? null,
            'pickup_fuel' => $data['pickup_fuel'],
            'return_fuel' => $data['return_fuel'] ?? null,
            'base_amount' => $data['base_amount'] ?? 0,
            'extra_km_charge' => $data['extra_km_charge'] ?? 0,
            'late_return_charge' => $data['late_return_charge'] ?? 0,
            'damage_charge' => $data['damage_charge'] ?? 0,
            'fuel_charge' => $data['fuel_charge'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'status' => $data['status'] ?? 'scheduled',
            'pickup_notes' => $data['pickup_notes'] ?? null,
            'return_notes' => $data['return_notes'] ?? null,
            'damage_notes' => $data['damage_notes'] ?? null,

        ]);

        return $trip->load([
            'customer',
            'vehicle',
            'driver',
            'pickupBranch',
            'dropBranch',
            'pickupStaff',
            'returnStaff',
            'cancelledBy',
        ]);
    }

    /**
     * Update trip.
     */
    public function update(Trip $trip, array $data): Trip
    {
        $trip->update($data);

        return $trip->fresh([
            'customer',
            'vehicle',
            'driver',
            'pickupBranch',
            'dropBranch',
            'pickupStaff',
            'returnStaff',
            'cancelledBy',
        ]);
    }

    /**
     * Check if vehicle has an active trip.
     */
    public function hasActiveTripForVehicle(
        int $vehicleId,
        ?int $ignoreTripId = null
    ): bool {

        return Trip::where('vehicle_id', $vehicleId)
            ->whereIn('status', [
                'picked_up',
                'on_trip',
            ])
            ->when(
                $ignoreTripId,
                fn($q) => $q->where('id', '!=', $ignoreTripId)
            )
            ->exists();
    }
}