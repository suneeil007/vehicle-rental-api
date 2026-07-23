<?php

namespace App\Modules\User\Repositories\Contracts;

use App\Modules\User\Models\UserProfile;

interface UserProfileRepositoryInterface
{
    /**
     * Create profile.
     */
    public function create(
        array $data
    ): UserProfile;

    /**
     * Update profile.
     */
    public function update(
        UserProfile $profile,
        array $data
    ): UserProfile;

    /**
     * Find profile by slug.
     */
    public function findBySlug(
        string $slug
    ): ?UserProfile;
}