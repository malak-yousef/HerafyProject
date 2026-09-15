<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CraftmanDiscoveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => is_string($this->query('search')) ? trim($this->query('search')) : $this->query('search'),
            'sort' => $this->query('sort', 'newest'),
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'profession_id' => [
                'nullable',
                'integer',
                Rule::exists('professions', 'id')->where('status', 'active')->whereNull('deleted_at'),
            ],
            'sort' => ['nullable', Rule::in([
                'newest', 'name_asc', 'name_desc', 'experience_desc', 'experience_asc',
            ])],
        ];
    }
}
