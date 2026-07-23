<?php

namespace App\Modules\Vehicle\Repositories;

use App\Modules\Vehicle\Models\VehicleCategory;
use App\Modules\Vehicle\Repositories\Contracts\VehicleCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleCategoryRepository implements VehicleCategoryRepositoryInterface
{

    public function getAll(array $filters = [])
    {
        return VehicleCategory::latest()->paginate(10);
    }


    public function getById(int $id): ?VehicleCategory
    {
        return VehicleCategory::find($id);
    }


    public function create(array $data): VehicleCategory
    {
        return VehicleCategory::create($data);
    }



    public function update(
            VehicleCategory $category,
            array $data
        ): VehicleCategory {

            $category->fill($data);
            $category->save();
            return $category;
        }


    public function delete(
        VehicleCategory $category
    ): bool {

        return $category->delete();
    }


    public function existsBySlug(string $slug): bool
    {
        return VehicleCategory::where('slug',$slug)->exists();
    }

    public function existsByName(
            string $name,
            ?int $excludeId = null
        ): bool {

            $query = VehicleCategory::where('name', $name);

            if ($excludeId !== null) {
                $query->where('id', '!=', $excludeId);
            }

            return $query->exists();
        }

    public function findBySlug(string $slug): ?VehicleCategory
    {
        return VehicleCategory::where('slug',$slug)->first();
    }
    

}