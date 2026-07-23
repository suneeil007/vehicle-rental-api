<?php

namespace App\Modules\Vehicle\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Helpers\ApiResponse;

use App\Modules\Vehicle\Models\VehicleCategory;
use App\Modules\Vehicle\Requests\StoreVehicleCategoryRequest;
use App\Modules\Vehicle\Requests\UpdateVehicleCategoryRequest;
use App\Modules\Vehicle\Resources\VehicleCategoryResource;
use App\Modules\Vehicle\Services\VehicleCategoryService;



class VehicleCategoryController extends Controller
{
    public function __construct(
        protected VehicleCategoryService $vehicleCategoryService
    ) {}

    /**
     * Display all vehicle categories.
     */
    public function index(): JsonResponse
    {
        $categories = $this->vehicleCategoryService->getAll();

        return ApiResponse::success(
            VehicleCategoryResource::collection($categories),
            'Vehicle categories retrieved successfully.'
        );
    }

    /**
     * Store a new vehicle category.
     */
    public function store(
        StoreVehicleCategoryRequest $request
    ): JsonResponse {

        $category = $this->vehicleCategoryService->create(
            $request->validated()
        );

        return ApiResponse::created(
            new VehicleCategoryResource($category),
            'Vehicle category created successfully.'
        );
    }

    /**
     * Display a single vehicle category.
     */
    public function show(
        VehicleCategory $vehicleCategory
    ): JsonResponse {

        return ApiResponse::success(
            new VehicleCategoryResource($vehicleCategory),
            'Vehicle category retrieved successfully.'
        );
    }

    /**
     * Update a vehicle category.
     */
    public function update(
        UpdateVehicleCategoryRequest $request,
        VehicleCategory $vehicleCategory
    ): JsonResponse {

        $category = $this->vehicleCategoryService->update(
            $vehicleCategory,
            $request->validated()
        );

        return ApiResponse::updated(
            new VehicleCategoryResource($category),
            'Vehicle category updated successfully.'
        );
    }

    /**
     * Delete a vehicle category.
     */
    public function destroy(
        VehicleCategory $vehicleCategory
    ): JsonResponse {

        $this->vehicleCategoryService->delete($vehicleCategory);

        return ApiResponse::deleted(
            'Vehicle category deleted successfully.'
        );
    }
}