<?php

namespace App\Modules\Invoice\Repositories\Contracts;

use App\Modules\Invoice\Models\Invoice;

interface InvoiceRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?Invoice;

    public function findBySlug(string $slug): ?Invoice;

    public function create(array $data): Invoice;

    public function update(Invoice $invoice, array $data): Invoice;
}