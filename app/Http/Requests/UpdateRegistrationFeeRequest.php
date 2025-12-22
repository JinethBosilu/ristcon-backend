<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationFeeRequest extends FormRequest
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
            'attendee_type' => 'sometimes|string|max:255',
            'currency' => 'sometimes|string|size:3',
            'amount' => 'sometimes|numeric|min:0',
            'early_bird_amount' => 'nullable|numeric|min:0',
            'early_bird_deadline' => 'nullable|date',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'currency.size' => 'Currency must be a 3-letter code (e.g., USD, LKR).',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be zero or greater.',
        ];
    }
}
