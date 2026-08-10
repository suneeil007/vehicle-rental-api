<?php

namespace App\Modules\Vehicle\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVehicleRequest extends FormRequest
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
                'required',
                'exists:vehicle_categories,id'
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'brand' => [
                'required',
                'string',
                'max:255'
            ],

            'model' => [
                'required',
                'string',
                'max:255'
            ],

            'manufacture_year' => [
                'nullable',
                'digits:4'
            ],

            'transmission' => [
                'required',
                'in:manual,automatic'
            ],

            'fuel_type' => [
                'required',
                'in:petrol,diesel,electric,hybrid'
            ],

            'seat_capacity' => [
                'required',
                'integer',
                'min:1'
            ],

            'price_per_day' => [
                'required',
                'numeric',
                'min:0'
            ],

            'registration_number' => [
                'required',
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
                'required',
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

            'featured_new_index' => ['nullable', 'integer', 'min:0'],

        ];
    }
}