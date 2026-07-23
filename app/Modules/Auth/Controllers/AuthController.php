<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Helpers\ApiResponse;

use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\LoginRequest;

use App\Modules\Auth\Services\AuthService;

use App\Modules\Auth\Resources\UserResource;



class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(
        RegisterRequest $request
    ): JsonResponse {

        $user = $this->authService->register(
            $request->validated()
        );

        return ApiResponse::created(
            new UserResource($user),
            'User registered successfully.'
        );
    }

    /**
     * Log in a user.
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {

        $result = $this->authService->login(
            $request->validated()
        );

        return ApiResponse::success(
            [
                'token' => $result['token'],
                'user' => new UserResource(
                    $result['user']->load([
                        'role',
                        'branch',
                    ])
                ),
            ],
            'Login successful.'
        );
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(
        Request $request
    ): JsonResponse {

        $this->authService->logout(
            $request->user()
        );

        return ApiResponse::success(
            null,
            'Logged out successfully.'
        );
    }
}