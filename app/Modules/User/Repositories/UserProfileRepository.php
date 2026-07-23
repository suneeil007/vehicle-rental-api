<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\UserProfile;
use App\Modules\User\Repositories\Contracts\UserProfileRepositoryInterface;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    /**
     * Create profile.
     */
    public function create(
        array $data
    ): UserProfile {

        return UserProfile::create($data);

    }

    /**
     * Update profile.
     */
    public function update(
        UserProfile $profile,
        array $data
    ): UserProfile {

        $profile->update($data);

        return $profile->fresh();

    }

    /**
     * Find profile by slug.
     */
    public function findBySlug(
        string $slug
    ): ?UserProfile {

        return UserProfile::where(
            'slug',
            $slug
        )->first();

    }
}