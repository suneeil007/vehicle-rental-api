<?php

namespace App\Modules\Branch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    /**
     * Authorize the request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:191',
            ],

            'code' => [
                'required',
                'string',
                'max:20',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:191',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'city' => [
                'nullable',
                'string',
                'max:191',
            ],

            'state' => [
                'nullable',
                'string',
                'max:191',
            ],

            'country' => [
                'nullable',
                'string',
                'max:191',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'latitude' => [
                'nullable',
                'numeric',
            ],

            'longitude' => [
                'nullable',
                'numeric',
            ],

            'opening_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'closing_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'manager_name' => [
                'nullable',
                'string',
                'max:191',
            ],

            'manager_phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],

        ];
    }
}