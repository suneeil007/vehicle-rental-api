<?php

namespace App\Modules\Booking\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_at' => ['sometimes', 'date'],
            'expected_return_at' => ['sometimes', 'date'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}