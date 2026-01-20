<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FindPetsByStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|array',
            'status.*' => ['string', Rule::in(['available', 'pending', 'sold'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status jest wymagany',
            'status.array' => 'Status musi być tablicą',
            'status.*.string' => 'Każdy status musi być tekstem',
            'status.*.in' => 'Nieprawidłowy status. Dozwolone: available, pending, sold',
        ];
    }

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
