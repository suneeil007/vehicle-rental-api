<?php

namespace App\Modules\Vehicle\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehicleImage;
use App\Modules\Vehicle\Requests\StoreVehicleImageRequest;
use App\Modules\Vehicle\Requests\UpdateVehicleImageRequest;
use App\Modules\Vehicle\Resources\VehicleImageResource;
use App\Modules\Vehicle\Services\VehicleImageService;
use Illuminate\Http\JsonResponse;

class VehicleImageController extends Controller
{
    public function __construct(
        private VehicleImageService $vehicleImageService
    ) {}

    /**
     * List all vehicle images.
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success(
            VehicleImageResource::collection(
                VehicleImage::with('vehicle')->latest()->paginate(20)
            )
        );
    }

    /**
     * Show a single vehicle image.
     */
    public function show(VehicleImage $vehicleImage): JsonResponse
    {
        return ApiResponse::success(
            new VehicleImageResource($vehicleImage)
        );
    }

    /**
     * Add one or more images to an existing vehicle.
     */
    public function store(StoreVehicleImageRequest $request): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($request->validated('vehicle_id'));

        $this->vehicleImageService->storeMany(
            $vehicle,
            $request->file('images', [])
        );

        return ApiResponse::success(
            VehicleImageResource::collection(
                $vehicle->fresh(['images'])->images
            ),
            'Images uploaded successfully.',
            201
        );
    }

    /**
     * Update a single image's metadata (is_featured / sort_order).
     */
    public function update(
        UpdateVehicleImageRequest $request,
        VehicleImage $vehicleImage
    ): JsonResponse {

        $image = $this->vehicleImageService->updateOne(
            $vehicleImage,
            $request->validated()
        );

        return ApiResponse::success(
            new VehicleImageResource($image),
            'Image updated successfully.'
        );
    }

    /**
     * Delete a single image.
     */
    public function destroy(VehicleImage $vehicleImage): JsonResponse
    {
        $this->vehicleImageService->deleteOne($vehicleImage);

        return ApiResponse::success(
            null,
            'Image deleted successfully.'
        );
    }
}   

