<?php

namespace App\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;

use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Payment\Requests\StorePaymentRequest;
use App\Modules\Payment\Resources\PaymentResource;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * List payments.
     */
    public function index(Request $request): JsonResponse
    {
        $payments = $this->paymentService->getAll(
            $request->all()
        );

        return ApiResponse::success(
            PaymentResource::collection($payments),
            'Payments retrieved successfully.'
        );
    }

    /**
     * Store payment.
     */
    public function store(
        StorePaymentRequest $request
    ): JsonResponse {

        $payment = $this->paymentService->create([

            ...$request->validated(),

            'received_by' => auth()->id(),
            'status' => 'paid',
            'paid_at' => now(),

        ]);

        return ApiResponse::created(
            new PaymentResource($payment),
            'Payment recorded successfully.'
        );
    }

    /**
     * Show payment.
     */
    public function show(
        Payment $payment
    ): JsonResponse {

        return ApiResponse::success(
            new PaymentResource($payment),
            'Payment retrieved successfully.'
        );
    }
}