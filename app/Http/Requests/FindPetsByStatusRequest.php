<?php

namespace App\Http\Requests;

use App\Enums\PetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request validation for finding pets by status
 *
 * Validates and sanitizes the status parameter used to filter pets.
 */
class FindPetsByStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     *
     * @return bool Always returns true as this endpoint is public
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     *
     * @return array<string, mixed> Validation rules
     */
    public function rules(): array
    {
        return [
            'status' => 'required|array',
            'status.*' => ['string', Rule::in(PetStatus::values())],
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string> Custom error messages in Polish
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status jest wymagany',
            'status.array' => 'Status musi być tablicą',
            'status.*.string' => 'Każdy status musi być tekstem',
            'status.*.in' => 'Nieprawidłowy status. Dozwolone: available, pending, sold',
        ];
    }

    /**
     * Prepare the data for validation
     *
     * Sanitizes status values by removing HTML tags and trimming whitespace.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $status = $this->input('status', []);
        
        if (is_array($status)) {
            $this->merge([
                'status' => array_map(fn($value) => strip_tags(trim($value)), $status)
            ]);
        }
    }
}
