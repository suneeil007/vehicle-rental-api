<?php

namespace App\Modules\Payment\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,

            // Booking
            'booking' => $this->whenLoaded('booking', function () {
                return [
                    'id' => $this->booking->id,
                    'slug' => $this->booking->slug,
                    'status' => $this->booking->status,
                ];
            }),

            // Trip
            'trip' => $this->whenLoaded('trip', function () {
                return [
                    'id' => $this->trip->id,
                    'slug' => $this->trip->slug,
                    'status' => $this->trip->status,
                ];
            }),

            // Payment details
            'amount' => $this->amount,
            'type' => $this->type,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
            'status' => $this->status,

            // Staff
            'received_by' => $this->whenLoaded('receivedBy', function () {
                return [
                    'id' => $this->receivedBy->id,
                    'slug' => $this->receivedBy->slug,
                    'name' => $this->receivedBy->name,
                ];
            }),

            'paid_at' => $this->paid_at,
            'notes' => $this->notes,

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}