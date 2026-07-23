<?php

namespace App\Modules\Payment\Services;

use Illuminate\Support\Facades\DB;

use App\Exceptions\NotFoundException;

use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(
        protected PaymentRepositoryInterface $repository
    ) {}

    /**
     * List payments.
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get payment by slug.
     */
    public function getBySlug(string $slug): Payment
    {
        $payment = $this->repository->findBySlug($slug);

        if (!$payment) {
            throw new NotFoundException(
                'Payment not found.'
            );
        }

        return $payment;
    }

    /**
     * Create payment.
     */
    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {

            return $this->repository->create($data);

        });
    }
}