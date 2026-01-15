<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConferenceQueryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:upcoming,ongoing,completed',
            'year' => 'sometimes|integer|min:2020|max:2100',
            'include' => 'sometimes|string',
            'type' => 'sometimes|string',
            'category' => 'sometimes|string',
            'available' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: upcoming, ongoing, or completed.',
            'year.integer' => 'Year must be a valid integer.',
            'year.min' => 'Year must be 2020 or later.',
            'year.max' => 'Year must be 2100 or earlier.',
        ];
    }
}
