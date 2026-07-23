<?php

namespace App\Modules\Payment\Repositories;

use Illuminate\Support\Str;

use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Payment::query()
            ->with([
                'booking',
                'trip',
                'receivedBy',
            ])
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    public function getById(int $id): ?Payment
    {
        return Payment::with([
            'booking',
            'trip',
            'receivedBy',
        ])->find($id);
    }

    public function findBySlug(string $slug): ?Payment
    {
        return Payment::with([
            'booking',
            'trip',
            'receivedBy',
        ])->where('slug', $slug)->first();
    }

    public function create(array $data): Payment
    {
        $payment = Payment::create([

            'slug' => (string) Str::uuid(),

            'booking_id' => $data['booking_id'] ?? null,
            'trip_id' => $data['trip_id'] ?? null,

            'amount' => $data['amount'],
            'type' => $data['type'],
            'payment_method' => $data['payment_method'],

            'transaction_reference' =>
                $data['transaction_reference'] ?? null,

            'status' => $data['status'] ?? 'paid',

            'received_by' =>
                $data['received_by'] ?? null,

            'paid_at' =>
                $data['paid_at'] ?? now(),

            'notes' => $data['notes'] ?? null,
        ]);

        return $payment->load([
            'booking',
            'trip',
            'receivedBy',
        ]);
    }

    public function update(
        Payment $payment,
        array $data
    ): Payment {

        $payment->update($data);

        return $payment->fresh([
            'booking',
            'trip',
            'receivedBy',
        ]);
    }
}