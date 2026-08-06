<?php

namespace App\Modules\User\Services;

use Illuminate\Support\Facades\DB;

use App\Exceptions\NotFoundException;

use App\Modules\User\Models\UserProfile;
use App\Modules\User\Repositories\Contracts\UserProfileRepositoryInterface;

class UserProfileService
{
    public function __construct(
        protected UserProfileRepositoryInterface $repository
    ) {}

    /**
     * Create profile.
     * Used internally during user registration.
     */
    public function create(array $data): UserProfile
    {
        return DB::transaction(function () use ($data) {

            return $this->repository->create($data);

        });
    }

    /**
     * Get profile by slug.
     */
    public function findBySlug(
        string $slug
    ): UserProfile {

        $profile = $this->repository->findBySlug($slug);

        if (!$profile) {
            throw new NotFoundException(
                'User profile not found.'
            );
        }

        return $profile;
    }

   

    /**
 * Update profile.
 */
   
    public function update(
                UserProfile $profile,
                array $data
            ): UserProfile {

                // \Log::info('=== REAL METHOD RUNNING ===', ['data' => $data]);
                return DB::transaction(function () use ($profile, $data) {
                    $actingUser = auth()->user();
                    // Restrict role_id and 'suspended' status to Super Admins only
                    if (!$actingUser?->isSuperAdmin()) {
                        unset($data['role_id']);
                        if (($data['status'] ?? null) === 'suspended') {
                            unset($data['status']);
                        }
                    }

                    // Unwrap nested "profile" object sent from the frontend
                    $profileInput = $data['profile'] ?? [];
                    unset($data['profile']);

                    $userData = array_intersect_key(
                        $data,
                        array_flip(['name', 'email', 'phone', 'role_id', 'branch_id', 'status'])
                    );

                    if (!empty($userData)) {
                        $profile->user()->update($userData);
                    }

                    return $this->repository->update(
                        $profile,
                        $profileInput
                    )->load('user.role');

             });

        }    

}