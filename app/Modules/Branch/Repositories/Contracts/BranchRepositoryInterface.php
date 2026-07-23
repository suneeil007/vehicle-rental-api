<?php

namespace App\Modules\Branch\Repositories\Contracts;

use App\Modules\Branch\Models\Branch;

interface BranchRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?Branch;

    public function create(array $data): Branch;

    public function update(
        Branch $branch,
        array $data
    ): Branch;

    public function delete(
        Branch $branch
    ): bool;

    public function existsByCode(
        string $code,
        ?int $ignoreId = null
    ): bool;

    public function existsByEmail(
        string $email,
        ?int $ignoreId = null
    ): bool;
}