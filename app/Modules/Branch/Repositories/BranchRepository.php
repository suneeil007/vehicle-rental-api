<?php

namespace App\Modules\Branch\Repositories;

use Illuminate\Support\Str;

use App\Modules\Branch\Models\Branch;
use App\Modules\Branch\Repositories\Contracts\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    /**
     * List branches.
     */
    public function getAll(array $filters = [])
    {
        return Branch::query()
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    /**
     * Get branch by ID.
     */
    public function getById(
        int $id
    ): ?Branch {

        return Branch::find($id);

    }

    /**
     * Create branch.
     */
    public function create(
        array $data
    ): Branch {

        return Branch::create([

            'slug' => (string) Str::uuid(),
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? 'Nepal',
            'postal_code' => $data['postal_code'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'opening_time' => $data['opening_time'] ?? null,
            'closing_time' => $data['closing_time'] ?? null,
            'manager_name' => $data['manager_name'] ?? null,
            'manager_phone' => $data['manager_phone'] ?? null,
            'status' => $data['status'] ?? 'active',

        ]);

    }

    /**
     * Update branch.
     */
    public function update(
        Branch $branch,
        array $data
    ): Branch {

        $branch->update([

            'name' => $data['name'] ?? $branch->name,
            'code' => isset($data['code'])
                ? strtoupper($data['code'])
                : $branch->code,
            'phone' => $data['phone'] ?? $branch->phone,
            'email' => $data['email'] ?? $branch->email,
            'address' => $data['address'] ?? $branch->address,
            'city' => $data['city'] ?? $branch->city,
            'state' => $data['state'] ?? $branch->state,
            'country' => $data['country'] ?? $branch->country,
            'postal_code' => $data['postal_code'] ?? $branch->postal_code,
            'latitude' => $data['latitude'] ?? $branch->latitude,
            'longitude' => $data['longitude'] ?? $branch->longitude,
            'opening_time' => $data['opening_time'] ?? $branch->opening_time,
            'closing_time' => $data['closing_time'] ?? $branch->closing_time,
            'manager_name' => $data['manager_name'] ?? $branch->manager_name,
            'manager_phone' => $data['manager_phone'] ?? $branch->manager_phone,
            'status' => $data['status'] ?? $branch->status,

        ]);

        return $branch->fresh();

    }

    /**
     * Delete branch.
     */
    public function delete(
        Branch $branch
    ): bool {

        return $branch->delete();

    }

    /**
     * Check duplicate code.
     */
    public function existsByCode(
        string $code,
        ?int $ignoreId = null
    ): bool {

        return Branch::where('code', strtoupper($code))
            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();

    }

    /**
     * Check duplicate email.
     */
    public function existsByEmail(
        string $email,
        ?int $ignoreId = null
    ): bool {

        return Branch::where('email', $email)
            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();

    }
}