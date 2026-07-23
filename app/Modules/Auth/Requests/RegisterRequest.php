<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest
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
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],

            'role_id' => [
                'nullable',
                'exists:roles,id'
            ],

            'branch_id' => [
                'nullable',
                'exists:branches,id'
            ],

        ];
    }


    protected function failedValidation(
        Validator $validator
    ) {

        throw new HttpResponseException(

            response()->json([

                'success' => false,

                'message' => 'Validation failed.',

                'errors' => $validator->errors()

            ], 422)

        );

    }
}