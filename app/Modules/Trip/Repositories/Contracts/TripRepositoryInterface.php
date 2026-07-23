<?php

namespace App\Modules\Trip\Repositories\Contracts;

use App\Modules\Trip\Models\Trip;

interface TripRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?Trip;

    public function create(array $data): Trip;

    public function update(
        Trip $trip,
        array $data
    ): Trip;

    // public function delete(
    //     Trip $trip
    // ): bool;

    /**
     * Check if vehicle already has an active trip.
     */
    public function hasActiveTripForVehicle(
        int $vehicleId,
        ?int $ignoreTripId = null
    ): bool;
}