<?php

namespace App\Modules\Vehicle\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateVehicleRequest extends FormRequest
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

            'vehicle_category_id' => [
                'sometimes',
                'exists:vehicle_categories,id'
            ],

            'name' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'brand' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'model' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'manufacture_year' => [
                'nullable',
                'digits:4'
            ],

            'transmission' => [
                'sometimes',
                'in:manual,automatic'
            ],

            'fuel_type' => [
                'sometimes',
                'in:petrol,diesel,electric,hybrid'
            ],

            'seat_capacity' => [
                'sometimes',
                'integer',
                'min:1'
            ],

            'price_per_day' => [
                'sometimes',
                'numeric',
                'min:0'
            ],

            'registration_number' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'mileage' => [
                'nullable',
                'integer'
            ],

            'color' => [
                'nullable',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'status' => [
                'sometimes',
                'in:available,booked,maintenance,inactive'
            ],

            'images' => [
                'nullable',
                'array',
                'max:8',
            ],

            'images.*' => [
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],

            'removed_image_ids' => [
                'nullable',
                'array',
            ],

            'removed_image_ids.*' => [
                'integer',
                'exists:vehicle_images,id',
            ],

        ];
    }
}