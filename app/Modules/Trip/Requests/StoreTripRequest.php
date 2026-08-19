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

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */

            'customer_id' => [
                'required',
                'exists:users,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Vehicle
            |--------------------------------------------------------------------------
            */

            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Rental Type
            |--------------------------------------------------------------------------
            */

            'rental_type' => [
                'required',
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
            | Pickup Branch
            |--------------------------------------------------------------------------
            */

            'pickup_branch_id' => [
                'nullable',
                'exists:branches,id',
                'required_if:rental_type,with_driver',
            ],

            /*
            |--------------------------------------------------------------------------
            | Pickup Location
            |--------------------------------------------------------------------------
            */

            'pickup_location' => [
                'nullable',
                'string',
                'max:500',
                'required_if:rental_type,self_drive',
            ],

            /*
            |--------------------------------------------------------------------------
            | Drop Branch
            |--------------------------------------------------------------------------
            */

            'drop_branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Drop Location
            |--------------------------------------------------------------------------
            */

            'drop_location' => [
                'nullable',
                'string',
                'max:500',
                'required_if:rental_type,self_drive',
            ],

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            'pickup_at' => [
                'required',
                'date',
            ],

            'expected_return_at' => [
                'required',
                'date',
                'after:pickup_at',
            ],

            /*
            |--------------------------------------------------------------------------
            | Pickup Vehicle Condition
            |--------------------------------------------------------------------------
            */

            'pickup_odometer' => [
                'required',
                'integer',
                'min:0',
            ],

            'pickup_fuel' => [
                'required',
                'in:empty,quarter,half,three_quarter,full',
            ],

            /*
            |--------------------------------------------------------------------------
            | Base Amount
            |--------------------------------------------------------------------------
            */

            'base_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Charges
            |--------------------------------------------------------------------------
            */

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
     * Additional business validation.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $rentalType = $this->input('rental_type');


            /*
            |--------------------------------------------------------------------------
            | WITH DRIVER
            |--------------------------------------------------------------------------
            */

            if ($rentalType === 'with_driver') {

                /*
                 * Driver is required.
                 */
                if (!$this->filled('driver_id')) {
                    $validator->errors()->add(
                        'driver_id',
                        'Driver is required for a driver trip.'
                    );
                }

                /*
                 * Pickup branch is required.
                 */
                if (!$this->filled('pickup_branch_id')) {
                    $validator->errors()->add(
                        'pickup_branch_id',
                        'Pickup branch is required for a driver trip.'
                    );
                }

                /*
                 * Location fields must not be used.
                 */
                if ($this->filled('pickup_location')) {
                    $validator->errors()->add(
                        'pickup_location',
                        'Pickup location cannot be used for a driver trip.'
                    );
                }

                if ($this->filled('drop_location')) {
                    $validator->errors()->add(
                        'drop_location',
                        'Drop location cannot be used for a driver trip.'
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SELF DRIVE
            |--------------------------------------------------------------------------
            */

            if ($rentalType === 'self_drive') {

                /*
                 * Driver must not be assigned.
                 */
                if ($this->filled('driver_id')) {
                    $validator->errors()->add(
                        'driver_id',
                        'Driver cannot be assigned to a self drive trip.'
                    );
                }

                /*
                 * Pickup location is required.
                 */
                if (!$this->filled('pickup_location')) {
                    $validator->errors()->add(
                        'pickup_location',
                        'Pickup location is required for a self drive trip.'
                    );
                }

                /*
                 * Drop location is required.
                 */
                if (!$this->filled('drop_location')) {
                    $validator->errors()->add(
                        'drop_location',
                        'Drop location is required for a self drive trip.'
                    );
                }

                /*
                 * Branch fields must not be used.
                 */
                if ($this->filled('pickup_branch_id')) {
                    $validator->errors()->add(
                        'pickup_branch_id',
                        'Pickup branch cannot be used for a self drive trip.'
                    );
                }

                if ($this->filled('drop_branch_id')) {
                    $validator->errors()->add(
                        'drop_branch_id',
                        'Drop branch cannot be used for a self drive trip.'
                    );
                }
            }
        });
    }
}