<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'branch_id' => ['sometimes', 'integer', Rule::exists('branches', 'id')],
            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive', 'suspended'])],

            // UserProfile fields — nested under "profile" to match frontend payload shape
            'profile' => ['sometimes', 'array'],

            'profile.date_of_birth' => ['sometimes', 'nullable', 'date'],
            'profile.gender' => ['sometimes', 'nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'profile.profile_photo' => ['nullable', 'string', 'max:255'],
            'profile.citizenship_no' => ['nullable', 'string', 'max:100'],
            'profile.passport_no' => ['nullable', 'string', 'max:100'],
            'profile.driving_license_no' => ['nullable', 'string', 'max:100'],
            'profile.license_expiry' => ['nullable', 'date'],
            'profile.nationality' => ['nullable', 'string', 'max:100'],
            'profile.address' => ['nullable', 'string'],
            'profile.city' => ['nullable', 'string', 'max:100'],
            'profile.state' => ['nullable', 'string', 'max:100'],
            'profile.country' => ['nullable', 'string', 'max:100'],
            'profile.postal_code' => ['nullable', 'string', 'max:20'],
            'profile.emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'profile.emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'profile.bio' => ['nullable', 'string', 'max:1000'],
        ];
    }
}