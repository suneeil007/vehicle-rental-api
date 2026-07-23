<?php

namespace App\Modules\Vehicle\Services;

use App\Exceptions\ConflictException;
use App\Modules\Vehicle\Models\VehicleCategory;
use App\Modules\Vehicle\Repositories\Contracts\VehicleCategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleCategoryService
{
    public function __construct(
        protected VehicleCategoryRepositoryInterface $repository
    ) {}

    /**
     * Get all categories.
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get category by ID.
     */
    public function getById(int $id): ?VehicleCategory
    {
        return $this->repository->getById($id);
    }

    /**
     * Get active categories.
     */
    public function getActive()
    {
        return $this->repository->getActive();
    }

    /**
     * Create category.
     */
    public function create(array $data): VehicleCategory
    {
        return DB::transaction(function () use ($data) {

            if ($this->repository->existsByName($data['name'])) {
                throw new ConflictException(
                    'Category name already exists.'
                );
            }

            $data['slug'] = (string) Str::uuid();

            return $this->repository->create($data);
        });
    }

    /**
     * Update category.
     */
    public function update(
        VehicleCategory $category,
        array $data
    ): VehicleCategory {

        return DB::transaction(function () use ($category, $data) {

            if (
                isset($data['name']) &&
                $this->repository->existsByName(
                    $data['name'],
                    $category->id
                )
            ) {
                throw new ConflictException(
                    'Category name already exists.'
                );
            }

            return $this->repository->update(
                $category,
                $data
            );
        });
    }

    /**
     * Delete category.
     */
    public function delete(
        VehicleCategory $category
    ): bool {

        return DB::transaction(function () use ($category) {

            if ($category->vehicles()->exists()) {
                throw new ConflictException(
                    'Cannot delete category because vehicles are assigned to it.'
                );
            }

            return $this->repository->delete($category);
        });
    }

    /**
     * Search categories.
     */
    public function search(string $keyword)
    {
        return $this->repository->search($keyword);
    }
}