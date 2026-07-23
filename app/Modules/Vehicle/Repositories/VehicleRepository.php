<?php

namespace App\Modules\Vehicle\Repositories;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehicleCategory;
use App\Modules\Vehicle\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VehicleRepository implements VehicleRepositoryInterface
{
    /**
     * Base query with relationships.
     */
    protected function query()
    {
        return Vehicle::with([
            'category',
            'images'
        ]);
    }

    /**
     * List all vehicles.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->query()
            ->latest()
            ->paginate(10);
    }

    /**
     * Search vehicles.
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['keyword']}%")
                  ->orWhere('brand', 'like', "%{$filters['keyword']}%")
                  ->orWhere('model', 'like', "%{$filters['keyword']}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where(
                'vehicle_category_id',
                $filters['category_id']
            );
        }

        if (!empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        if (!empty($filters['fuel_type'])) {
            $query->where(
                'fuel_type',
                $filters['fuel_type']
            );
        }

        if (!empty($filters['transmission'])) {
            $query->where(
                'transmission',
                $filters['transmission']
            );
        }

        return $query
            ->latest()
            ->paginate(10);
    }

    /**
     * Get vehicle by id.
     */
    public function getById(int $id): ?Vehicle
    {
        return $this->query()->find($id);
    }

    /**
     * Find by slug.
     */
    public function findBySlug(string $slug): ?Vehicle
    {
        return $this->query()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create vehicle.
     */
    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    /**
     * Update vehicle.
     */
    public function update(
        Vehicle $vehicle,
        array $data
    ): Vehicle {

        $vehicle->update($data);

        return $vehicle->fresh([
            'category',
            'images'
        ]);
    }

    /**
     * Delete vehicle.
     */
    public function delete(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }

    /**
     * Slug already exists?
     */
    public function existsBySlug(string $slug): bool
    {
        return Vehicle::where('slug', $slug)
            ->exists();
    }

    /**
     * Registration already exists?
     */
    public function existsByRegistration(
        string $registration
    ): bool {

        return Vehicle::where(
            'registration_number',
            $registration
        )->exists();
    }

    /**
     * Active vehicle categories.
     */
    public function getCategories(): Collection
    {
        return VehicleCategory::where('status', true)
            ->orderBy('name')
            ->get();
    }
}