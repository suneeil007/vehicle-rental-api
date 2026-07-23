<?php

namespace App\Modules\Booking\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:users,id',

            'vehicle_id' => 'required|exists:vehicles,id',

            'rental_type' => 'required|in:self_drive,with_driver',

            'pickup_branch_id' => 'required|exists:branches,id',

            'drop_branch_id' => 'nullable|exists:branches,id',

            'pickup_at' => 'required|date|after:now',

            'expected_return_at' => 'required|date|after:pickup_at',

            'quoted_amount' => 'required|numeric|min:0',

            'discount_amount' => 'nullable|numeric|min:0',

            'customer_notes' => 'nullable|string',
        ];
    }
}