<?php

namespace App\Modules\Booking\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;

use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Services\BookingService;

use App\Modules\Booking\Requests\StoreBookingRequest;
use App\Modules\Booking\Resources\BookingResource;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $service
    ) {}

    /**
     * List bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = $this->service->getAll(
            $request->all()
        );

        return ApiResponse::success(
            BookingResource::collection($bookings),
            'Bookings retrieved successfully.'
        );
    }

    /**
     * Store booking.
     */
    public function store(
        StoreBookingRequest $request
    ): JsonResponse {

        $booking = $this->service->create(
            $request->validated()
        );

        return ApiResponse::created(
            new BookingResource($booking),
            'Booking created successfully.'
        );
    }

    /**
     * Show single booking.
     */
    public function show(
        Booking $booking
    ): JsonResponse {

        return ApiResponse::success(
            new BookingResource($booking),
            'Booking retrieved successfully.'
        );
    }

    /**
     * Approve booking.
     */
    public function approve(
        Request $request,
        Booking $booking
    ): JsonResponse {

        $booking = $this->service->approve(
            $booking,
            auth()->id()
        );

        return ApiResponse::updated(
            new BookingResource($booking),
            'Booking approved successfully.'
        );
    }

    /**
     * Reject booking.
     */
    public function reject(
        Booking $booking
    ): JsonResponse {

        $booking = $this->service->reject($booking);

        return ApiResponse::updated(
            new BookingResource($booking),
            'Booking rejected successfully.'
        );
    }

    /**
     * Cancel booking.
     */
    public function cancel(
        Booking $booking
    ): JsonResponse {

        $booking = $this->service->cancel($booking);

        return ApiResponse::updated(
            new BookingResource($booking),
            'Booking cancelled successfully.'
        );
    }

    /**
     * Create trip from approved booking.
     */
    public function createTrip(
        Booking $booking
    ): JsonResponse {

        $booking = $this->service->createTrip($booking);

        return ApiResponse::updated(
            new BookingResource($booking),
            'Trip created from booking successfully.'
        );
    }
}