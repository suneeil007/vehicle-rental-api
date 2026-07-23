<?php

namespace App\Modules\Trip\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'customer_id' => [
                'required',
                'exists:users,id',
            ],

            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],

             'rental_type' => [
                'required',
                'in:self_drive,with_driver',
            ],

            'driver_id' => [
                'nullable',
                'exists:users,id',
                'required_if:rental_type,with_driver',
            ],

            'pickup_branch_id' => [
                'required',
                'exists:branches,id',
            ],

            'drop_branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            'pickup_at' => [
                'required',
                'date',
                'after_or_equal:now',
            ],

            'expected_return_at' => [
                'required',
                'date',
                'after:pickup_at',
            ],

            'pickup_odometer' => [
                'required',
                'integer',
                'min:0',
            ],

            'pickup_fuel' => [
                'required',
                'in:empty,quarter,half,three_quarter,full',
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

     public function messages(): array
        {
            return [

                'driver_id.required_if' =>
                    'Driver is required when rental type is with_driver.',

            ];
        }
}