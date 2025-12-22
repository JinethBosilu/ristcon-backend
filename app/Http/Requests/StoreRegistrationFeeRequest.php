<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationFeeRequest extends FormRequest
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
            'attendee_type' => 'required|string|max:255',
            'currency' => 'required|string|size:3', // ISO 4217 currency codes
            'amount' => 'required|numeric|min:0',
            'early_bird_amount' => 'nullable|numeric|min:0',
            'early_bird_deadline' => 'nullable|date|after:today',
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
            'attendee_type.required' => 'Attendee type is required.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be a 3-letter code (e.g., USD, LKR).',
            'amount.required' => 'Registration amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be zero or greater.',
            'early_bird_deadline.after' => 'Early bird deadline must be a future date.',
        ];
    }
}
