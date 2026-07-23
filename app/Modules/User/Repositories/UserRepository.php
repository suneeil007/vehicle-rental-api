<?php

namespace App\Modules\User\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return User::with([
                'role',
                'branch',
            ])
            ->latest()
            ->paginate(15);
    }

    public function getById(int $id): ?User
    {
        return User::with([
            'role',
            'branch',
        ])->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function existsByEmail(string $email, ?int $ignoreId = null): bool
    {
        return User::where('email', $email)
            ->when(
                $ignoreId,
                fn ($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();
    }

    public function existsByPhone(string $phone, ?int $ignoreId = null): bool
    {
        return User::where('phone', $phone)
            ->when(
                $ignoreId,
                fn ($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();
    }
}