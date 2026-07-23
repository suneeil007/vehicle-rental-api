<?php

namespace App\Modules\Invoice\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'invoice_number' => $this->invoice_number,

            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'slug' => $this->customer->slug,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),

            'trip' => $this->whenLoaded('trip', function () {
                return [
                    'id' => $this->trip->id,
                    'slug' => $this->trip->slug,
                    'status' => $this->trip->status,
                ];
            }),

            'subtotal' => $this->subtotal,
            'extra_km_charge' => $this->extra_km_charge,
            'late_return_charge' => $this->late_return_charge,
            'damage_charge' => $this->damage_charge,
            'fuel_charge' => $this->fuel_charge,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,

            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,

            'status' => $this->status,

            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,

            'generated_by' => $this->whenLoaded('generatedBy', function () {
                return [
                    'id' => $this->generatedBy->id,
                    'slug' => $this->generatedBy->slug,
                    'name' => $this->generatedBy->name,
                ];
            }),

            'generated_at' => $this->generated_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}