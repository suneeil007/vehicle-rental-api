<?php

namespace App\Modules\Vehicle\Services;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleService
{
    public function __construct(
        protected VehicleRepositoryInterface $repository,
        protected VehicleImageService $imageService
    ) {}

    /**
     * Get all vehicles.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return empty($filters)
            ? $this->repository->getAll()
            : $this->repository->search($filters);
    }

    /**
     * Search vehicles.
     */
    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository->search($filters);
    }

    /**
     * Get vehicle by ID.
     */
    public function getById(int $id): Vehicle
    {
        $vehicle = $this->repository->getById($id);

        if (! $vehicle) {
            throw new NotFoundException('Vehicle not found.');
        }

        return $vehicle;
    }

    /**
     * Get vehicle by slug.
     */
    public function findBySlug(string $slug): Vehicle
    {
        $vehicle = $this->repository->findBySlug($slug);

        if (! $vehicle) {
            throw new NotFoundException('Vehicle not found.');
        }

        return $vehicle;
    }

    /**
     * Create vehicle.
     */
    public function create(array $data, array $images = [], ?int $featuredIndex = null): Vehicle
    {
        return DB::transaction(function () use ($data, $images, $featuredIndex) {

            if (
                $this->repository->existsByRegistration(
                    $data['registration_number']
                )
            ) {
                throw new ConflictException(
                    'Registration number already exists.'
                );
            }

            $data['slug'] = $this->generateUniqueSlug();

            $vehicle = $this->repository->create($data);

            $this->imageService->storeMany($vehicle, $images, $featuredIndex);

            return $vehicle->fresh(['category', 'images']);

        });
    }

    /**
     * Update vehicle.
     */
    public function update(
        Vehicle $vehicle,
        array $data,
        array $images = [],
        array $removedImageIds = [],
        ?int $featuredIndex = null
    ): Vehicle {

        return DB::transaction(function () use ($vehicle, $data, $images, $removedImageIds, $featuredIndex) {

            if (
                isset($data['registration_number']) &&
                $data['registration_number'] !== $vehicle->registration_number &&
                $this->repository->existsByRegistration(
                    $data['registration_number']
                )
            ) {
                throw new ConflictException(
                    'Registration number already exists.'
                );
            }

            $vehicle = $this->repository->update(
                $vehicle,
                $data
            );

            $this->imageService->deleteMany($vehicle, $removedImageIds);
            $this->imageService->storeMany($vehicle, $images, $featuredIndex);

            return $vehicle->fresh(['category', 'images']);

        });
    }

    /**
     * Delete vehicle.
     */
    public function delete(Vehicle $vehicle): bool
    {
        return DB::transaction(function () use ($vehicle) {

            $this->imageService->deleteAllForVehicle($vehicle);

            return $this->repository->delete($vehicle);

        });
    }

    /**
     * Get active vehicle categories.
     */
    public function getCategories(): Collection
    {
        return $this->repository->getCategories();
    }

    /**
     * Generate unique UUID slug.
     */
    private function generateUniqueSlug(): string
    {
        do {

            $slug = (string) Str::uuid();

        } while (
            $this->repository->existsBySlug($slug)
        );

        return $slug;
    }
}