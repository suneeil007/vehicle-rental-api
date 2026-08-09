<?php

namespace App\Modules\Vehicle\Repositories\Contracts;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehicleImage;
use Illuminate\Database\Eloquent\Collection;

interface VehicleImageRepositoryInterface
{
    public function create(Vehicle $vehicle, array $attributes): VehicleImage;

    public function findManyByIds(Vehicle $vehicle, array $imageIds): Collection;

    public function findAllForVehicle(Vehicle $vehicle): Collection;

    public function update(VehicleImage $image, array $attributes): VehicleImage;

    public function delete(VehicleImage $image): bool;

    public function maxSortOrder(Vehicle $vehicle): int;

    public function hasImages(Vehicle $vehicle): bool;

    public function clearFeatured(Vehicle $vehicle): void;
}