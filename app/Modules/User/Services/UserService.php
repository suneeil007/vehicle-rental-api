<?php

namespace App\Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;

use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\User\Repositories\Contracts\UserProfileRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $repository,
        protected UserProfileRepositoryInterface $profileRepository,
    ) {}

    /**
     * List users.
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get single user by ID.
     */
    public function getById(int $id): User
    {
        $user = $this->repository->getById($id);

        if (!$user) {
            throw new NotFoundException(
                'User not found.'
            );
        }

        return $user;
    }

    /**
     * Create user.
     */
   public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            if ($this->repository->existsByEmail($data['email'])) {
                throw new ConflictException('Email already exists.');
            }

            if (!empty($data['phone']) && $this->repository->existsByPhone($data['phone'])) {
                throw new ConflictException('Phone already exists.');
            }

            $slug = (string) Str::uuid();
            $data['slug'] = $slug;

            $profileData = $data['profile'] ?? [];
            unset($data['profile']);

            // No manual hashing needed — the User model's 'password' => 'hashed' cast does this automatically
            $user = $this->repository->create($data);

            $this->profileRepository->create(array_merge($profileData, [
                'user_id' => $user->id,
                'slug' => $slug,
            ]));

            return $user->load(['role', 'branch', 'profile']);

        });
    }

    /**
     * Update user.
     */
    public function update(
        User $user,
        array $data
    ): User {

        return DB::transaction(function () use ($user, $data) {

            if (
                isset($data['email']) &&
                $this->repository->existsByEmail(
                    $data['email'],
                    $user->id
                )
            ) {
                throw new ConflictException(
                    'Email already exists.'
                );
            }

            if (
                !empty($data['phone']) &&
                $this->repository->existsByPhone(
                    $data['phone'],
                    $user->id
                )
            ) {
                throw new ConflictException(
                    'Phone already exists.'
                );
            }

            return $this->repository->update(
                $user,
                $data
            )->load([
                'role',
                'branch',
                'profile',
            ]);

        });
    }

    /**
     * Delete user.
     */
    public function delete(
        User $user
    ): bool {

        return DB::transaction(function () use ($user) {

            return $this->repository->delete($user);

        });

    }
}