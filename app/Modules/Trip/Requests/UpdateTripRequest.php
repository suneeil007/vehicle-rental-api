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

            /*
            |--------------------------------------------------------------------------
            | Customer / Vehicle
            |--------------------------------------------------------------------------
            */

            'customer_id' => [
                'sometimes',
                'exists:users,id',
            ],

            'vehicle_id' => [
                'sometimes',
                'exists:vehicles,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Rental Type
            |--------------------------------------------------------------------------
            */

            'rental_type' => [
                'sometimes',
                'in:self_drive,with_driver',
            ],

            /*
            |--------------------------------------------------------------------------
            | Driver
            |--------------------------------------------------------------------------
            */

            'driver_id' => [
                'nullable',
                'exists:users,id',
                'required_if:rental_type,with_driver',
            ],

            /*
            |--------------------------------------------------------------------------
            | Pickup / Drop Branch
            |--------------------------------------------------------------------------
            */

            'pickup_branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            'drop_branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Pickup / Drop Location
            |--------------------------------------------------------------------------
            */

            'pickup_location' => [
                'nullable',
                'string',
                'max:500',
            ],

            'drop_location' => [
                'nullable',
                'string',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            'pickup_at' => [
                'sometimes',
                'date',
            ],

            'expected_return_at' => [
                'sometimes',
                'date',
                'after:pickup_at',
            ],

            /*
            |--------------------------------------------------------------------------
            | Vehicle Condition
            |--------------------------------------------------------------------------
            */

            'pickup_odometer' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'return_odometer' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'pickup_fuel' => [
                'sometimes',
                'in:empty,quarter,half,three_quarter,full',
            ],

            'return_fuel' => [
                'nullable',
                'in:empty,quarter,half,three_quarter,full',
            ],

            /*
            |--------------------------------------------------------------------------
            | Billing
            |--------------------------------------------------------------------------
            */

            'base_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'extra_km_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'late_return_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'damage_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'fuel_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'total_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'pickup_notes' => [
                'nullable',
                'string',
            ],

            'return_notes' => [
                'nullable',
                'string',
            ],

            'damage_notes' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Validate that return odometer is not below pickup odometer.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $pickupOdometer = $this->input('pickup_odometer');
            $returnOdometer = $this->input('return_odometer');

            if (
                $pickupOdometer !== null &&
                $returnOdometer !== null &&
                $returnOdometer < $pickupOdometer
            ) {
                $validator->errors()->add(
                    'return_odometer',
                    'Return odometer cannot be less than pickup odometer.'
                );
            }
        });
    }
}