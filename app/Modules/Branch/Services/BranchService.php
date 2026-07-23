<?php

namespace App\Modules\Branch\Services;

use Illuminate\Support\Facades\DB;

use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;

use App\Modules\Branch\Models\Branch;
use App\Modules\Branch\Repositories\Contracts\BranchRepositoryInterface;

class BranchService
{
    public function __construct(
        protected BranchRepositoryInterface $repository
    ) {}

    /**
     * List branches.
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get branch by ID.
     */
    public function getById(int $id): Branch
    {
        $branch = $this->repository->getById($id);

        if (!$branch) {
            throw new NotFoundException(
                'Branch not found.'
            );
        }

        return $branch;
    }

    /**
     * Create branch.
     */
    public function create(array $data): Branch
    {
        return DB::transaction(function () use ($data) {

            if (
                $this->repository->existsByCode(
                    $data['code']
                )
            ) {
                throw new ConflictException(
                    'Branch code already exists.'
                );
            }

            if (
                !empty($data['email']) &&
                $this->repository->existsByEmail(
                    $data['email']
                )
            ) {
                throw new ConflictException(
                    'Email already exists.'
                );
            }

            return $this->repository->create($data);

        });
    }

    /**
     * Update branch.
     */
    public function update(
        Branch $branch,
        array $data
    ): Branch {

        return DB::transaction(function () use ($branch, $data) {

            if (
                isset($data['code']) &&
                $this->repository->existsByCode(
                    $data['code'],
                    $branch->id
                )
            ) {
                throw new ConflictException(
                    'Branch code already exists.'
                );
            }

            if (
                !empty($data['email']) &&
                $this->repository->existsByEmail(
                    $data['email'],
                    $branch->id
                )
            ) {
                throw new ConflictException(
                    'Email already exists.'
                );
            }

            return $this->repository->update(
                $branch,
                $data
            );

        });
    }

    /**
     * Delete branch.
     */
    public function delete(
        Branch $branch
    ): bool {

        return DB::transaction(function () use ($branch) {

            return $this->repository->delete(
                $branch
            );

        });
    }
}