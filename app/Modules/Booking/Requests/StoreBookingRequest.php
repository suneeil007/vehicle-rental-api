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
            | Pickup Branch
            |--------------------------------------------------------------------------
            |
            | Self Drive:
            |     Customer comes to the selected branch.
            |     Therefore branch is REQUIRED.
            |
            | With Driver:
            |     Driver goes to customer's pickup location.
            |     Therefore branch is NULL.
            |
            */

            'pickup_branch_id' => [
                'nullable',
                'exists:branches,id',
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
            | Pickup Location
            |--------------------------------------------------------------------------
            |
            | With Driver:
            |     Customer gives pickup location.
            |
            | Self Drive:
            |     Customer comes to branch.
            |     Therefore location is not required.
            |
            */

            'pickup_location' => [
                'nullable',
                'string',
                'max:500',
                'required_if:rental_type,with_driver',
            ],

            /*
            |--------------------------------------------------------------------------
            | Drop Location
            |--------------------------------------------------------------------------
            |
            | Optional.
            |
            */

            'drop_location' => [
                'nullable',
                'string',
                'max:500',
            ],

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'pickup_at' => [
                'required',
                'date',
                'after:now',
            ],

            'expected_return_at' => [
                'required',
                'date',
                'after:pickup_at',
            ],

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            'quoted_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'customer_notes' => [
                'nullable',
                'string',
            ],

            'admin_notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}