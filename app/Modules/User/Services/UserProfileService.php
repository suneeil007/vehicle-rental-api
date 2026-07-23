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
    public function update__(
        UserProfile $profile,
        array $data
    ): UserProfile {

        return DB::transaction(function () use ($profile, $data) {

            return $this->repository->update(
                $profile,
                $data
            );

        });
    }

    /**
 * Update profile.
 */
public function update(
    UserProfile $profile,
    array $data
): UserProfile {

    return DB::transaction(function () use ($profile, $data) {

        // Separate User-table fields from UserProfile-table fields
        $userData = array_intersect_key(
            $data,
            array_flip(['name', 'email', 'phone'])
        );

        $profileData = array_diff_key($data, $userData);

        if (!empty($userData)) {
            $profile->user()->update($userData);
        }

        return $this->repository->update(
            $profile,
            $profileData
        );

    });
}
}