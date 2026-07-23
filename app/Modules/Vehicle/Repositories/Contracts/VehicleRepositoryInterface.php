<?php

namespace App\Modules\Vehicle\Repositories\Contracts;

use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface VehicleRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;

    public function search(array $filters): LengthAwarePaginator;

    public function getById(int $id): ?Vehicle;

    public function findBySlug(string $slug): ?Vehicle;

    public function create(array $data): Vehicle;

    public function update(Vehicle $vehicle, array $data): Vehicle;

    public function delete(Vehicle $vehicle): bool;

    public function existsBySlug(string $slug): bool;

    public function existsByRegistration(string $registration): bool;

    public function getCategories(): Collection;
}