<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'status' => ['sometimes', 'string', Rule::in(['available', 'pending', 'sold'])],
            'photoUrls' => 'sometimes|array',
            'photoUrls.*' => 'string|url|max:2048',
            'category' => 'sometimes|array',
            'category.id' => 'required_with:category|integer|min:0',
            'category.name' => 'required_with:category|string|max:255',
            'tags' => 'sometimes|array',
            'tags.*.id' => 'required_with:tags|integer|min:0',
            'tags.*.name' => 'required_with:tags|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID zwierzaka jest wymagane',
            'id.integer' => 'ID musi być liczbą',
            'id.min' => 'ID musi być większe od 0',
            'name.required' => 'Nazwa zwierzaka jest wymagana',
            'name.string' => 'Nazwa musi być tekstem',
            'name.max' => 'Nazwa może mieć maksymalnie 255 znaków',
            'status.in' => 'Nieprawidłowy status. Dozwolone: available, pending, sold',
            'photoUrls.array' => 'photoUrls musi być tablicą',
            'photoUrls.*.url' => 'Każdy URL zdjęcia musi być prawidłowym adresem URL',
            'category.array' => 'Kategoria musi być obiektem',
            'category.id.required_with' => 'ID kategorii jest wymagane',
            'category.name.required_with' => 'Nazwa kategorii jest wymagana',
            'tags.array' => 'Tagi muszą być tablicą',
            'tags.*.id.required_with' => 'ID tagu jest wymagane',
            'tags.*.name.required_with' => 'Nazwa tagu jest wymagana',
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('name')) {
            $data['name'] = strip_tags(trim($this->input('name')));
        }

        if ($this->has('status')) {
            $data['status'] = strip_tags(trim($this->input('status')));
        }

        if ($this->has('photoUrls') && is_array($this->input('photoUrls'))) {
            $data['photoUrls'] = array_map(fn($url) => strip_tags(trim($url)), $this->input('photoUrls'));
        }

        if ($this->has('category') && is_array($this->input('category'))) {
            $category = $this->input('category');
            $data['category'] = [];
            
            if (isset($category['id'])) {
                $data['category']['id'] = $category['id'];
            }
            
            if (isset($category['name'])) {
                $data['category']['name'] = strip_tags(trim($category['name']));
            }
        }

        if ($this->has('tags') && is_array($this->input('tags'))) {
            $data['tags'] = array_map(function ($tag) {
                return [
                    'id' => $tag['id'] ?? null,
                    'name' => isset($tag['name']) ? strip_tags(trim($tag['name'])) : null,
                ];
            }, $this->input('tags'));
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}
