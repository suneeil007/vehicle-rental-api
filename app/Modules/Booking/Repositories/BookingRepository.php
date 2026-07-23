<?php

namespace App\Modules\Booking\Repositories;

use Illuminate\Support\Str;

use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    /**
     * List bookings.
     */
    public function getAll(array $filters = [])
    {
        return Booking::query()
            ->with([
                'customer',
                'vehicle',
                'pickupBranch',
                'dropBranch',
                'approvedBy',
                'trip',
            ])
            ->latest()
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    /**
     * Get booking by ID.
     */
    public function getById(int $id): ?Booking
    {
        return Booking::with([
                'customer',
                'vehicle',
                'pickupBranch',
                'dropBranch',
                'approvedBy',
                'trip',
            ])
            ->find($id);
    }

    /**
     * Find booking by slug.
     */
    public function findBySlug(string $slug): ?Booking
    {
        return Booking::with([
                'customer',
                'vehicle',
                'pickupBranch',
                'dropBranch',
                'approvedBy',
                'trip',
            ])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create booking.
     */
    public function create(array $data): Booking
    {
        $booking = Booking::create([

            'slug' => (string) Str::uuid(),

            'customer_id' => $data['customer_id'],
            'vehicle_id' => $data['vehicle_id'],

            'rental_type' => $data['rental_type'],

            'pickup_branch_id' => $data['pickup_branch_id'],
            'drop_branch_id' => $data['drop_branch_id'] ?? null,

            'pickup_at' => $data['pickup_at'],
            'expected_return_at' => $data['expected_return_at'],

            'quoted_amount' => $data['quoted_amount'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'final_amount' => $data['final_amount'] ?? 0,

            'status' => $data['status']
                ?? Booking::STATUS_PENDING,

            'customer_notes' => $data['customer_notes'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        return $booking->load([
            'customer',
            'vehicle',
            'pickupBranch',
            'dropBranch',
            'approvedBy',
            'trip',
        ]);
    }

    /**
     * Update booking.
     */
    public function update(
        Booking $booking,
        array $data
    ): Booking {

        $booking->update($data);

        return $booking->fresh([
            'customer',
            'vehicle',
            'pickupBranch',
            'dropBranch',
            'approvedBy',
            'trip',
        ]);
    }

    /**
     * Delete booking.
     */
    public function delete(Booking $booking): bool
    {
        return $booking->delete();
    }
}