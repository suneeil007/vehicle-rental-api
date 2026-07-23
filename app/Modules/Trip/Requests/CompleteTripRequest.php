<?php

namespace App\Modules\Trip\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'actual_return_at' => [
                'required',
                'date',
            ],

            'return_odometer' => [
                'required',
                'integer',
                'min:0',
            ],

            'return_fuel' => [
                'required',
                'in:empty,quarter,half,three_quarter,full',
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
}