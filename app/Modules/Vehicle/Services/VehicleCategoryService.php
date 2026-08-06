<?php

namespace App\Modules\Vehicle\Services;

use App\Exceptions\ConflictException;
use App\Modules\Vehicle\Models\VehicleCategory;
use App\Modules\Vehicle\Repositories\Contracts\VehicleCategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

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

            if (!empty($data['image'])) {
                $data['image'] = $this->uploadImage($data['image']);
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

                if (!empty($data['image'])) {
                    $newImage = $this->uploadImage($data['image']);
                    $this->deleteImage($category->image);
                    $data['image'] = $newImage;
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

            $this->deleteImage($category->image);

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


    private function uploadImage(UploadedFile $image): string
    {
        $destination = public_path('uploads/vehicle-categories');

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $extension = strtolower($image->getClientOriginalExtension());

        $fileName =
            Str::uuid() . '-' .
            Str::slug(
                pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
            ) .
            '.' .
            $extension;

        $image->move($destination, $fileName);

        return 'uploads/vehicle-categories/' . $fileName;
    }

    private function deleteImage(?string $imagePath): void
    {
        if (
            $imagePath &&
            File::exists(public_path($imagePath))
        ) {
            File::delete(public_path($imagePath));
        }
    }
}