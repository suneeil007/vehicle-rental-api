<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,suspended',
            ],

            'profile' => [
                'nullable',
                'array',
            ],

            'profile.date_of_birth' => [
                'nullable',
                'date',
            ],

            'profile.gender' => [
                'nullable',
                'in:male,female,other',
            ],

            'profile.citizenship_no' => [
                    'nullable',
                    'string',
                    'max:191',
                    'unique:user_profiles,citizenship_no',
                ],

                'profile.passport_no' => [
                    'nullable',
                    'string',
                    'max:191',
                    'unique:user_profiles,passport_no',
                ],

                'profile.driving_license_no' => [
                    'nullable',
                    'string',
                    'max:191',
                    'unique:user_profiles,driving_license_no',
                ],

            'profile.license_expiry' => [
                'nullable',
                'date',
            ],

            'profile.nationality' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.address' => [
                'nullable',
                'string',
            ],

            'profile.city' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.state' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.country' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.postal_code' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.emergency_contact_name' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.emergency_contact_phone' => [
                'nullable',
                'string',
                'max:191',
            ],

            'profile.bio' => [
                'nullable',
                'string',
            ],

        ];
    }
}