<?php

namespace App\Modules\Booking\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            | Rental Type
            |--------------------------------------------------------------------------
            */
            'rental_type' => $this->rental_type,

            /*
            |--------------------------------------------------------------------------
            | Pickup / Drop
            |--------------------------------------------------------------------------
            |
            | With Driver:
            |   pickup_branch / drop_branch are used.
            |
            | Self Drive:
            |   pickup_location / drop_location are used.
            |
            */
            'pickup_branch' => $this->whenLoaded('pickupBranch'),
            'drop_branch' => $this->whenLoaded('dropBranch'),

            'pickup_location' => $this->pickup_location,
            'drop_location' => $this->drop_location,

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */
            'pickup_at' => $this->pickup_at,
            'expected_return_at' => $this->expected_return_at,

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */
            'quoted_amount' => $this->quoted_amount,
            'discount_amount' => $this->discount_amount,
            'final_amount' => $this->final_amount,

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */
            'approved_by' => $this->whenLoaded('approvedBy'),
            'approved_at' => $this->approved_at,

            /*
            |--------------------------------------------------------------------------
            | Trip
            |--------------------------------------------------------------------------
            */
            'trip' => $this->whenLoaded('trip'),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            'status' => $this->status,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */
            'customer_notes' => $this->customer_notes,
            'admin_notes' => $this->admin_notes,

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