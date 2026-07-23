<?php

namespace App\Modules\Branch\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Helpers\ApiResponse;

use App\Modules\Branch\Models\Branch;
use App\Modules\Branch\Services\BranchService;
use App\Modules\Branch\Requests\StoreBranchRequest;
use App\Modules\Branch\Requests\UpdateBranchRequest;
use App\Modules\Branch\Resources\BranchResource;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService
    ) {}

    /**
     * List branches.
     */
    public function index(): JsonResponse
    {
        $branches = $this->branchService->getAll();

        return ApiResponse::success(
            BranchResource::collection($branches),
            'Branches retrieved successfully.'
        );
    }

    /**
     * Store branch.
     */
    public function store(
        StoreBranchRequest $request
    ): JsonResponse {

        $branch = $this->branchService->create(
            $request->validated()
        );

        return ApiResponse::created(
            new BranchResource($branch),
            'Branch created successfully.'
        );
    }

    /**
     * Show single branch.
     */
    public function show(
        Branch $branch
    ): JsonResponse {

        return ApiResponse::success(
            new BranchResource($branch),
            'Branch retrieved successfully.'
        );
    }

    /**
     * Update branch.
     */
    public function update(
        UpdateBranchRequest $request,
        Branch $branch
    ): JsonResponse {

        $branch = $this->branchService->update(
            $branch,
            $request->validated()
        );

        return ApiResponse::updated(
            new BranchResource($branch),
            'Branch updated successfully.'
        );
    }

    /**
     * Delete branch.
     */
    public function destroy(
        Branch $branch
    ): JsonResponse {

        $this->branchService->delete($branch);

        return ApiResponse::deleted(
            'Branch deleted successfully.'
        );
    }
}