<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Helpers\ApiResponse;

use App\Modules\Invoice\Services\InvoiceService;
use App\Modules\Invoice\Resources\InvoiceResource;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $service
    ) {}

    /**
     * List invoices.
     */
    public function index(): JsonResponse
    {
        $invoices = $this->service->getAll();

        return ApiResponse::success(
            InvoiceResource::collection($invoices),
            'Invoices retrieved successfully.'
        );
    }

    /**
     * Show single invoice.
     */
    public function show(string $slug): JsonResponse
    {
        $invoice = $this->service->findBySlug($slug);

        return ApiResponse::success(
            new InvoiceResource($invoice),
            'Invoice retrieved successfully.'
        );
    }
}