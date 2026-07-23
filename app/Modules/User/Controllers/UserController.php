<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use App\Models\User;

use App\Helpers\ApiResponse;

use App\Modules\User\Services\UserService;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserResource;

use App\Modules\User\Services\UserProfileService;
use App\Modules\User\Requests\UpdateUserProfileRequest;
use App\Modules\User\Resources\UserProfileResource;



class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UserProfileService $UserProfileService,
    ) {}

    /**
     * List users.
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->getAll();

        return ApiResponse::success(
            UserResource::collection($users),
            'Users retrieved successfully.'
        );
    }

    /**
     * Store user.
     */
    public function store(
        StoreUserRequest $request
    ): JsonResponse {

        $user = $this->userService->create(
            $request->validated()
        );

        return ApiResponse::created(
            new UserResource($user),
            'User created successfully.'
        );
    }

    /**
     * Show single user.
     */
    public function show(
        User $user
    ): JsonResponse {

        return ApiResponse::success(
            new UserResource(
                $user->load([
                    'role',
                    'branch',
                    'profile',
                ])
            ),
            'User retrieved successfully.'
        );
    }

    /**
     * Update user profile.
     */
    
    public function update(
    UpdateUserProfileRequest $request,
        string $user
    ): JsonResponse {

        $profile = $this->UserProfileService->findBySlug($user);

        $profile = $this->UserProfileService->update(
            $profile,
            $request->validated()
        );

        return ApiResponse::updated(
            new UserProfileResource($profile),
            'User profile updated successfully.'
        );
    }

    /**
 * Show user profile.
 */
public function profile(
        User $user
    ): JsonResponse {

        if (!$user->profile) {
            return ApiResponse::error(
                'User profile not found.',
                404
            );
        }

        return ApiResponse::success(
            new UserProfileResource($user->profile),
            'User profile retrieved successfully.'
        );
    }


    
}