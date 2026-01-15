<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'conference_id' => 'required|exists:conferences,id',
            'payment_type' => 'required|in:local,foreign',
            'beneficiary_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'swift_code' => 'nullable|string|max:11',
            'branch_code' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:500',
            'currency' => 'required|string|size:3',
            'additional_info' => 'nullable|string|max:1000',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'conference_id.required' => 'A conference must be selected.',
            'conference_id.exists' => 'The selected conference does not exist.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Payment type must be either local or foreign.',
            'beneficiary_name.required' => 'Beneficiary name is required.',
            'bank_name.required' => 'Bank name is required.',
            'account_number.required' => 'Account number is required.',
            'swift_code.max' => 'SWIFT code must not exceed 11 characters.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be a 3-letter code (e.g., USD, LKR).',
        ];
    }
}
