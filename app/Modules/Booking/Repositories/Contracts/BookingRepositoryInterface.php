<?php

namespace App\Modules\Booking\Repositories\Contracts;

use App\Modules\Booking\Models\Booking;

interface BookingRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?Booking;

    public function findBySlug(string $slug): ?Booking;

    public function create(array $data): Booking;

    public function update(Booking $booking, array $data): Booking;

    public function delete(Booking $booking): bool;
}