<?php

namespace App\Modules\Vehicle\Services;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehicleImage;
use App\Modules\Vehicle\Repositories\Contracts\VehicleImageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VehicleImageService
{
    public function __construct(
        protected VehicleImageRepositoryInterface $repository
    ) {}

    public function storeMany(Vehicle $vehicle, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $isFirstBatch = ! $this->repository->hasImages($vehicle);
        $sortOrder = $this->repository->maxSortOrder($vehicle);

        foreach ($files as $index => $file) {
            $path = $this->uploadImage($file);

            $this->repository->create($vehicle, [
                'image'       => $path,
                'is_featured' => $isFirstBatch && $index === 0,
                'sort_order'  => ++$sortOrder,
            ]);
        }
    }

    public function deleteMany(Vehicle $vehicle, array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = $this->repository->findManyByIds($vehicle, $imageIds);

        foreach ($images as $image) {
            $this->deleteImage($image->image);
            $this->repository->delete($image);
        }
    }

    /**
     * Delete every image belonging to a vehicle — used when the vehicle itself is deleted.
     */
    public function deleteAllForVehicle(Vehicle $vehicle): void
    {
        $images = $this->repository->findAllForVehicle($vehicle);

        foreach ($images as $image) {
            $this->deleteImage($image->image);
            $this->repository->delete($image);
        }
    }

    public function updateOne(VehicleImage $image, array $attributes): VehicleImage
    {
        return DB::transaction(function () use ($image, $attributes) {

            if (! empty($attributes['is_featured'])) {
                $this->repository->clearFeatured($image->vehicle);
            }

            return $this->repository->update($image, $attributes);

        });
    }

    public function deleteOne(VehicleImage $image): void
    {
        $this->deleteImage($image->image);

        $this->repository->delete($image);
    }

    private function uploadImage(UploadedFile $image): string
    {
        $destination = public_path('uploads/vehicles');

        if (! File::exists($destination)) {
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

        return 'uploads/vehicles/' . $fileName;
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