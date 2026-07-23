<?php

namespace App\Modules\Payment\Repositories\Contracts;

use App\Modules\Payment\Models\Payment;

interface PaymentRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?Payment;

    public function findBySlug(string $slug): ?Payment;

    public function create(array $data): Payment;

    public function update(Payment $payment, array $data): Payment;
}