<?php

namespace App\Modules\Trip\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,

            /*
            |--------------------------------------------------------------------------
            | Customer & Vehicle
            |--------------------------------------------------------------------------
            */

            'customer' => $this->whenLoaded('customer'),

            'vehicle' => $this->whenLoaded('vehicle'),

            /*
            |--------------------------------------------------------------------------
            | Rental
            |--------------------------------------------------------------------------
            */

            'rental_type' => $this->rental_type,

            'driver' => $this->whenLoaded('driver'),

            /*
            |--------------------------------------------------------------------------
            | Branch / Location
            |--------------------------------------------------------------------------
            */

            'pickup_branch' => $this->whenLoaded('pickupBranch'),

            'pickup_location' => $this->pickup_location,

            'drop_branch' => $this->whenLoaded('dropBranch'),

            'drop_location' => $this->drop_location,

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            'pickup_at' => $this->pickup_at,

            'expected_return_at' => $this->expected_return_at,

            'actual_return_at' => $this->actual_return_at,

            /*
            |--------------------------------------------------------------------------
            | Vehicle Condition
            |--------------------------------------------------------------------------
            */

            'pickup_odometer' => $this->pickup_odometer,

            'return_odometer' => $this->return_odometer,

            'distance' => $this->distance,

            'pickup_fuel' => $this->pickup_fuel,

            'return_fuel' => $this->return_fuel,

            /*
            |--------------------------------------------------------------------------
            | Billing
            |--------------------------------------------------------------------------
            */

            'base_amount' => $this->base_amount,

            'extra_km_charge' => $this->extra_km_charge,

            'late_return_charge' => $this->late_return_charge,

            'damage_charge' => $this->damage_charge,

            'fuel_charge' => $this->fuel_charge,

            'total_amount' => $this->total_amount,

            /*
            |--------------------------------------------------------------------------
            | Staff
            |--------------------------------------------------------------------------
            */

            'pickup_staff' => $this->whenLoaded('pickupStaff'),

            'return_staff' => $this->whenLoaded('returnStaff'),

            /*
            |--------------------------------------------------------------------------
            | Cancellation
            |--------------------------------------------------------------------------
            */

            'cancelled_by' => $this->whenLoaded('cancelledBy'),

            'cancellation_reason' => $this->cancellation_reason,

            'cancelled_at' => $this->cancelled_at,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'pickup_notes' => $this->pickup_notes,

            'return_notes' => $this->return_notes,

            'damage_notes' => $this->damage_notes,

            /*
            |--------------------------------------------------------------------------
            | Booking
            |--------------------------------------------------------------------------
            */

            'booking' => $this->whenLoaded('booking'),

            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */

            'payments' => $this->whenLoaded('payments'),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => $this->status,

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}