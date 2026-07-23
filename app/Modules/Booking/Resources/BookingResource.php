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

            'customer' => $this->whenLoaded('customer'),
            'vehicle' => $this->whenLoaded('vehicle'),

            'rental_type' => $this->rental_type,

            'pickup_branch' => $this->whenLoaded('pickupBranch'),
            'drop_branch' => $this->whenLoaded('dropBranch'),

            'pickup_at' => $this->pickup_at,
            'expected_return_at' => $this->expected_return_at,

            'quoted_amount' => $this->quoted_amount,
            'discount_amount' => $this->discount_amount,
            'final_amount' => $this->final_amount,

            'approved_by' => $this->whenLoaded('approvedBy'),
            'approved_at' => $this->approved_at,

            'trip' => $this->whenLoaded('trip'),

            'status' => $this->status,

            'customer_notes' => $this->customer_notes,
            'admin_notes' => $this->admin_notes,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}