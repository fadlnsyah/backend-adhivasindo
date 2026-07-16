<?php

namespace App\Http\Requests\Content;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexContentRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('per_page') && (int) $this->input('per_page') > 100) {
            $this->merge([
                'per_page' => 100,
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->errorResponse('Validation Error', $validator->errors(), 422)
        );
    }
}
