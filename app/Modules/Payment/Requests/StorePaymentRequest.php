<?php

namespace App\Modules\Payment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'booking_id' =>
                'nullable|exists:bookings,id',

            'trip_id' =>
                'nullable|exists:trips,id',

            'amount' =>
                'required|numeric|min:1',

            'type' =>
                'required|in:advance,deposit,final,refund',

            'payment_method' =>
                'required|in:cash,card,bank_transfer,esewa,khalti',

            'transaction_reference' =>
                'nullable|string|max:255',

            'notes' =>
                'nullable|string',
        ];
    }
}