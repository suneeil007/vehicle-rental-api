<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Authorize the request.
     */
    public function authorize(): bool
    {
        return true;
    }  

    // UpdateUserProfileRequest
public function rules(): array
{
    return [
        // User table fields (optional on profile update)
        'name'  => ['sometimes', 'string', 'max:255'],
        'email' => [
            'sometimes',
            'email',
            Rule::unique('users', 'email')->ignore($this->route('user'), 'slug'),
        ],
        'phone' => ['sometimes', 'string', 'max:20'],

        // Role & status
        'role_id' => ['sometimes', 'integer', Rule::exists('roles', 'id')],
        'status' => ['sometimes', 'string', Rule::in(['active', 'inactive', 'suspended'])],

        // UserProfile table fields
        'date_of_birth' => ['sometimes', 'date'],
        'gender' => ['sometimes', 'string'],
                'profile_photo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'citizenship_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'passport_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'driving_license_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'license_expiry' => [
                'nullable',
                'date',
            ],

            'nationality' => [
                'nullable',
                'string',
                'max:100',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
    ];
}


}