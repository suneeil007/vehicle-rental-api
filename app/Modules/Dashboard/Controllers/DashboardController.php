<?php

namespace App\Modules\Dashboard\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;

use App\Modules\Dashboard\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $service
    ) {}

    /**
     * Dashboard summary.
     */
    public function summary(): JsonResponse
    {
        return ApiResponse::success(
            $this->service->getSummary(),
            'Dashboard summary retrieved successfully.'
        );
    }

    /**
     * Revenue chart.
     */
    public function revenueChart(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);

        return ApiResponse::success(
            $this->service->getRevenueChart($days),
            'Revenue chart retrieved successfully.'
        );
    }

    /**
     * Vehicle utilization.
     */
    public function vehicleUtilization(): JsonResponse
    {
        return ApiResponse::success(
            $this->service->getVehicleUtilization(),
            'Vehicle utilization retrieved successfully.'
        );
    }
}