<?php

namespace App\Modules\Vehicle\Repositories\Contracts;

use App\Modules\Vehicle\Models\VehicleCategory;

interface VehicleCategoryRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?VehicleCategory;

    public function create(array $data): VehicleCategory;

    public function update(
        VehicleCategory $category,
        array $data
    ): VehicleCategory;

    public function delete(
        VehicleCategory $category
    ): bool;

    public function existsBySlug(string $slug): bool;


    public function findBySlug(string $slug): ?VehicleCategory;

    public function existsByName(
            string $name,
            ?int $excludeId = null
        ): bool;
}