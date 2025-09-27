<?php

namespace App\Http\Requests\ApiKeys;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('api_keys.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'scopes' => ['nullable', 'array'],
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ];
    }
}
