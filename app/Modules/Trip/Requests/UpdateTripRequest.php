<?php

namespace App\Modules\Trip\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'rental_type' => [
                'sometimes',
                'in:self_drive,with_driver',
            ],

            'driver_id' => [
                'nullable',
                'exists:users,id',
                'required_if:rental_type,with_driver',
            ],

            'pickup_at' => [
                'sometimes',
                'date',
            ],

            'expected_return_at' => [
                'sometimes',
                'date',
                'after:pickup_at',
            ],

            'drop_branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            'base_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'pickup_notes' => [
                'nullable',
                'string',
            ],

        ];
    }
}