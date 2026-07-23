<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;

use App\Modules\Auth\Repositories\Contracts\AuthRepositoryInterface;
use App\Modules\User\Repositories\Contracts\UserProfileRepositoryInterface;

class AuthService
{
    public function __construct(
        protected AuthRepositoryInterface $repository,
        protected UserProfileRepositoryInterface $profileRepository,
    ) {}

    /**
     * Register new user.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Generate UUID
            |--------------------------------------------------------------------------
            */

            $slug = (string) Str::uuid();

            $data['slug'] = $slug;

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $user = $this->repository->create($data);

            /*
            |--------------------------------------------------------------------------
            | Create User Profile
            |--------------------------------------------------------------------------
            */

            $this->profileRepository->create([
                'user_id' => $user->id,
                'slug'    => $slug,
            ]);

            return $user->load([
                'role',
                'branch',
                'profile',
            ]);
        });
    }

    /**
     * Login user.
     */
    public function login(array $data): array
    {
        $user = $this->repository->findByEmail($data['email']);

        if (
            !$user ||
            !Hash::check($data['password'], $user->password)
        ) {
            throw new UnauthorizedException(
                'Invalid email or password.'
            );
        }

        if ($user->status !== 'active') {
            throw new ForbiddenException(
                'Your account is inactive.'
            );
        }

        $token = $user
            ->createToken('api-token')
            ->plainTextToken;

        return [
            'user' => $user->load([
                'role',
                'branch',
                'profile',
            ]),
            'token' => $token,
        ];
    }

    /**
     * Logout user.
     */
    public function logout(User $user): bool
    {
        return (bool) $user
            ->currentAccessToken()
            ?->delete();
    }
}