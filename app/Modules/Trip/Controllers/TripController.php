<?php

namespace App\Modules\Trip\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Helpers\ApiResponse;

use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;
use App\Modules\Trip\Resources\TripResource;

use App\Modules\Trip\Requests\StoreTripRequest;
use App\Modules\Trip\Requests\UpdateTripRequest;
use App\Modules\Trip\Requests\CompleteTripRequest;

use App\Modules\Invoice\Services\InvoiceService;
use App\Modules\Invoice\Resources\InvoiceResource;

class TripController extends Controller
{
    public function __construct(
        protected TripService $tripService
    ) {}

    /**
     * List trips.
     */
    public function index(Request $request): JsonResponse
    {
        $trips = $this->tripService->getAll(
            $request->all()
        );

        return ApiResponse::success(
            TripResource::collection($trips),
            'Trips retrieved successfully.'
        );
    }

    /**
     * Create trip.
     */
    public function store(
        StoreTripRequest $request
    ): JsonResponse {

        $trip = $this->tripService->create(
            $request->validated()
        );

        return ApiResponse::created(
            new TripResource($trip),
            'Trip created successfully.'
        );
    }

    /**
     * Show trip.
     */
    public function show(
        Trip $trip
    ): JsonResponse {

        $trip->load([
            'customer',
            'vehicle',
            'driver',
            'pickupBranch',
            'dropBranch',
            'pickupStaff',
            'returnStaff',
            'cancelledBy',
            'booking',
            'payments',
        ]);

        return ApiResponse::success(
            new TripResource($trip),
            'Trip retrieved successfully.'
        );
    }

    /**
     * Update trip.
     */
    public function update(
        UpdateTripRequest $request,
        Trip $trip
    ): JsonResponse {

        $trip = $this->tripService->update(
            $trip,
            $request->validated()
        );

        return ApiResponse::updated(
            new TripResource($trip),
            'Trip updated successfully.'
        );
    }

    /**
     * Pickup trip.
     *
     * Assign staff and hand over vehicle.
     */
    public function pickup(
        Request $request,
        Trip $trip
    ): JsonResponse {

        $trip = $this->tripService->pickup(
            $trip,
            $request->user()->id
        );

        return ApiResponse::success(
            new TripResource($trip),
            'Vehicle handed over successfully.'
        );
    }

    /**
     * Start trip.
     *
     * Vehicle is now on road.
     */
    public function start(
        Trip $trip
    ): JsonResponse {

        $trip = $this->tripService->start(
            $trip
        );

        return ApiResponse::success(
            new TripResource($trip),
            'Trip started successfully.'
        );
    }

    /**
     * Complete trip.
     *
     * Vehicle returned and final bill calculated.
     */
    public function complete(
        CompleteTripRequest $request,
        Trip $trip
    ): JsonResponse {

        $trip = $this->tripService->complete(
            $trip,
            $request->validated(),
            $request->user()->id
        );

        return ApiResponse::success(
            new TripResource($trip),
            'Trip completed successfully.'
        );
    }

    /**
     * Cancel trip.
     */
    public function cancel(
        Request $request,
        Trip $trip
    ): JsonResponse {

        $request->validate([
            'reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $trip = $this->tripService->cancel(
            $trip,
            $request->input('reason'),
            $request->user()->id
        );

        return ApiResponse::success(
            new TripResource($trip),
            'Trip cancelled successfully.'
        );
    }

    /**
     * Get trips belonging to authenticated customer.
     */
    public function myTrips(
        Request $request
    ): JsonResponse {

        $trips = $request->user()
            ->customerTrips()
            ->with([
                'customer',
                'vehicle',
                'driver',
                'pickupBranch',
                'dropBranch',
                'booking',
                'payments',
            ])
            ->latest()
            ->paginate(10);

        return ApiResponse::success(
            TripResource::collection($trips),
            'My trips fetched successfully.'
        );
    }

    /**
     * Generate invoice from completed trip.
     */
    public function generateInvoice(
        Trip $trip,
        InvoiceService $invoiceService
    ): JsonResponse {

        $invoice = $invoiceService->generateFromTrip(
            $trip,
            auth()->id()
        );

        return ApiResponse::success(
            new InvoiceResource($invoice),
            'Invoice generated successfully.'
        );
    }
}