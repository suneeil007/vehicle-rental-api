<?php

namespace App\Modules\Invoice\Services;

use Illuminate\Support\Facades\DB;

use App\Exceptions\NotFoundException;
use App\Exceptions\ConflictException;

use App\Modules\Trip\Models\Trip;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Invoice\Repositories\Contracts\InvoiceRepositoryInterface;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepositoryInterface $repository
    ) {}

    /**
     * List invoices.
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get invoice by slug.
     */
    public function findBySlug(string $slug): Invoice
    {
        $invoice = $this->repository->findBySlug($slug);

        if (!$invoice) {
            throw new NotFoundException('Invoice not found.');
        }

        return $invoice;
    }

    /**
     * Generate invoice from completed trip.
     */
    public function generateFromTrip(
        Trip $trip,
        int $staffId
    ): Invoice {

        return DB::transaction(function () use ($trip, $staffId) {

            // Trip must be completed
            if (!$trip->isCompleted()) {
                throw new ConflictException(
                    'Invoice can only be generated for completed trips.'
                );
            }

            // Prevent duplicate invoice
            $existing = Invoice::where('trip_id', $trip->id)->first();

            if ($existing) {
                return $existing->load([
                    'trip',
                    'customer',
                    'generatedBy',
                ]);
            }

            // Calculate total
            $total =
                $trip->base_amount
                + $trip->extra_km_charge
                + $trip->late_return_charge
                + $trip->damage_charge
                + $trip->fuel_charge;

            // Sum payments
            $paid = $trip->payments()->sum('amount');

            // Due amount
            $due = max(0, $total - $paid);

            // Invoice status
            $status = match (true) {
                $paid <= 0 => Invoice::STATUS_ISSUED,
                $paid < $total => Invoice::STATUS_PARTIALLY_PAID,
                default => Invoice::STATUS_PAID,
            };

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            return $this->repository->create([

                'invoice_number' => $invoiceNumber,

                'trip_id' => $trip->id,
                'customer_id' => $trip->customer_id,

                'subtotal' => $trip->base_amount,
                'extra_km_charge' => $trip->extra_km_charge,
                'late_return_charge' => $trip->late_return_charge,
                'damage_charge' => $trip->damage_charge,
                'fuel_charge' => $trip->fuel_charge,

                'discount_amount' => 0,
                'tax_amount' => 0,

                'total_amount' => $total,

                'paid_amount' => $paid,
                'due_amount' => $due,

                'status' => $status,

                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),

                'generated_by' => $staffId,
                'generated_at' => now(),
            ]);
        });
    }

    /**
     * Generate sequential invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        $year = now()->year;

        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $next = $lastInvoice
            ? ((int) substr($lastInvoice->invoice_number, -4)) + 1
            : 1;

        return sprintf(
            'INV-%s-%04d',
            $year,
            $next
        );
    }
}