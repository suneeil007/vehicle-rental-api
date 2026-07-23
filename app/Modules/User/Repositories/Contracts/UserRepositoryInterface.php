<?php

namespace App\Modules\User\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;

    public function getById(int $id): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function existsByEmail(string $email, ?int $ignoreId = null): bool;

    public function existsByPhone(string $phone, ?int $ignoreId = null): bool;
}