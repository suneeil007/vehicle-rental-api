<?php

namespace App\Modules\Vehicle\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Requests\StoreVehicleRequest;
use App\Modules\Vehicle\Requests\UpdateVehicleRequest;
use App\Modules\Vehicle\Resources\VehicleResource;
use App\Modules\Vehicle\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $vehicles = $this->vehicleService->getAll(
            $request->all()
        );

        return ApiResponse::success(
            VehicleResource::collection($vehicles)
        );
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleService->create(
            $request->validated(),
            $request->file('images', []),
            $request->input('featured_new_index')
        );

        return ApiResponse::success(new VehicleResource($vehicle), 'Vehicle created successfully.', 201);
    }


    public function show(Vehicle $vehicle): JsonResponse
    {
        return ApiResponse::success(
            new VehicleResource($vehicle)
        );
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $vehicle = $this->vehicleService->update(
            $vehicle,
            $request->validated(),
            $request->file('images', []),
            $request->input('removed_image_ids', []),
            $request->input('featured_new_index')
        );

        return ApiResponse::success(new VehicleResource($vehicle), 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $this->vehicleService->delete($vehicle);

        return ApiResponse::success(
            null,
            'Vehicle deleted successfully.'
        );
    }
}