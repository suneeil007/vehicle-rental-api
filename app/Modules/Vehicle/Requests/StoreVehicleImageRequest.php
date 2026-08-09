<?php

namespace App\Modules\Vehicle\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVehicleImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function rules(): array
    {
        return [

            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],

            'images' => [
                'required',
                'array',
                'min:1',
                'max:8',
            ],

            'images.*' => [
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],

        ];
    }
}