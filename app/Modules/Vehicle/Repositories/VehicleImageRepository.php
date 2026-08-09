<?php

namespace App\Modules\Vehicle\Repositories;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehicleImage;
use App\Modules\Vehicle\Repositories\Contracts\VehicleImageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VehicleImageRepository implements VehicleImageRepositoryInterface
{
    public function create(Vehicle $vehicle, array $attributes): VehicleImage
    {
        return $vehicle->images()->create($attributes);
    }

    public function findManyByIds(Vehicle $vehicle, array $imageIds): Collection
    {
        return $vehicle->images()
            ->whereIn('id', $imageIds)
            ->get();
    }

    public function update(VehicleImage $image, array $attributes): VehicleImage
    {
        $image->update($attributes);

        return $image->fresh();
    }

    public function delete(VehicleImage $image): bool
    {
        return $image->delete();
    }

    public function maxSortOrder(Vehicle $vehicle): int
    {
        return (int) ($vehicle->images()->max('sort_order') ?? 0);
    }

    public function hasImages(Vehicle $vehicle): bool
    {
        return $vehicle->images()->exists();
    }

    public function clearFeatured(Vehicle $vehicle): void
    {
        $vehicle->images()
            ->where('is_featured', true)
            ->update(['is_featured' => false]);
    }

    public function findAllForVehicle(Vehicle $vehicle): Collection
    {
        return $vehicle->images()->get();
    }
}