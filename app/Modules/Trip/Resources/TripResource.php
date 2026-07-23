<?php

namespace App\Modules\Trip\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'slug' => $this->slug,

            // Customer
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'slug' => $this->customer->slug,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),

            // Vehicle
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle->id,
                    'slug' => $this->vehicle->slug,
                    'name' => $this->vehicle->name,
                    'registration_no' => $this->vehicle->registration_no,
                ];
            }),

            // Rental type
            'rental_type' => $this->rental_type,

            // Driver
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'slug' => $this->driver->slug,
                    'name' => $this->driver->name,
                    'email' => $this->driver->email,
                    'phone' => $this->driver->phone,
                ];
            }),

            // Pickup Branch
            'pickup_branch' => $this->whenLoaded('pickupBranch', function () {
                return [
                    'id' => $this->pickupBranch->id,
                    'slug' => $this->pickupBranch->slug,
                    'name' => $this->pickupBranch->name,
                    'city' => $this->pickupBranch->city,
                ];
            }),

            // Drop Branch
            'drop_branch' => $this->whenLoaded('dropBranch', function () {
                return [
                    'id' => $this->dropBranch->id,
                    'slug' => $this->dropBranch->slug,
                    'name' => $this->dropBranch->name,
                    'city' => $this->dropBranch->city,
                ];
            }),

            // Pickup Staff
            'pickup_staff' => $this->whenLoaded('pickupStaff', function () {
                return [
                    'id' => $this->pickupStaff->id,
                    'slug' => $this->pickupStaff->slug,
                    'name' => $this->pickupStaff->name,
                ];
            }),

            // Return Staff
            'return_staff' => $this->whenLoaded('returnStaff', function () {
                return [
                    'id' => $this->returnStaff->id,
                    'slug' => $this->returnStaff->slug,
                    'name' => $this->returnStaff->name,
                ];
            }),

            // Cancelled By
            'cancelled_by' => $this->whenLoaded('cancelledBy', function () {
                return [
                    'id' => $this->cancelledBy->id,
                    'slug' => $this->cancelledBy->slug,
                    'name' => $this->cancelledBy->name,
                ];
            }),

            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at,

            // Schedule
            'pickup_at' => $this->pickup_at,
            'expected_return_at' => $this->expected_return_at,
            'actual_return_at' => $this->actual_return_at,

            // Odometer
            'pickup_odometer' => $this->pickup_odometer,
            'return_odometer' => $this->return_odometer,
            'distance' => $this->distance,

            // Fuel
            'pickup_fuel' => $this->pickup_fuel,
            'return_fuel' => $this->return_fuel,

            // Charges
            'base_amount' => $this->base_amount,
            'extra_km_charge' => $this->extra_km_charge,
            'late_return_charge' => $this->late_return_charge,
            'damage_charge' => $this->damage_charge,
            'fuel_charge' => $this->fuel_charge,
            'total_amount' => $this->total_amount,

            // Status
            'status' => $this->status,
            'is_active' => $this->isActive(),
            'is_completed' => $this->isCompleted(),

            // Notes
            'pickup_notes' => $this->pickup_notes,
            'return_notes' => $this->return_notes,
            'damage_notes' => $this->damage_notes,

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}