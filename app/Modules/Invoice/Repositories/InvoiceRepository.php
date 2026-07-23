<?php

namespace App\Modules\Invoice\Repositories;

use Illuminate\Support\Str;

use App\Modules\Invoice\Models\Invoice;
use App\Modules\Invoice\Repositories\Contracts\InvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * List invoices.
     */
    public function getAll(array $filters = [])
    {
        return Invoice::query()
            ->with([
                'trip',
                'customer',
                'generatedBy',
            ])
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    /**
     * Get invoice by ID.
     */
    public function getById(int $id): ?Invoice
    {
        return Invoice::with([
            'trip',
            'customer',
            'generatedBy',
        ])->find($id);
    }

    /**
     * Find invoice by slug.
     */
    public function findBySlug(string $slug): ?Invoice
    {
        return Invoice::with([
            'trip',
            'customer',
            'generatedBy',
        ])->where('slug', $slug)->first();
    }

    /**
     * Create invoice.
     */
    public function create(array $data): Invoice
    {
        $invoice = Invoice::create([

            'slug' => (string) Str::uuid(),

            'invoice_number' => $data['invoice_number'],

            'trip_id' => $data['trip_id'],
            'customer_id' => $data['customer_id'],

            'subtotal' => $data['subtotal'] ?? 0,
            'extra_km_charge' => $data['extra_km_charge'] ?? 0,
            'late_return_charge' => $data['late_return_charge'] ?? 0,
            'damage_charge' => $data['damage_charge'] ?? 0,
            'fuel_charge' => $data['fuel_charge'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,

            'paid_amount' => $data['paid_amount'] ?? 0,
            'due_amount' => $data['due_amount'] ?? 0,

            'status' => $data['status'] ?? Invoice::STATUS_DRAFT,

            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? null,

            'pdf_path' => $data['pdf_path'] ?? null,
            'notes' => $data['notes'] ?? null,

            'generated_by' => $data['generated_by'] ?? null,
            'generated_at' => $data['generated_at'] ?? now(),
        ]);

        return $invoice->load([
            'trip',
            'customer',
            'generatedBy',
        ]);
    }

    /**
     * Update invoice.
     */
    public function update(
        Invoice $invoice,
        array $data
    ): Invoice {

        $invoice->update($data);

        return $invoice->fresh([
            'trip',
            'customer',
            'generatedBy',
        ]);
    }
}