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

        ];
    }
}