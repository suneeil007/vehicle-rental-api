<?php

namespace App\Modules\Vehicle\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'sometimes',
                'boolean',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'status.boolean' => 'Status must be true or false.',
            'image.image' => 'Please upload a valid image.',
            'image.mimes' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.',
            'image.max' => 'The image size must not exceed 2 MB.',

        ];
    }
}