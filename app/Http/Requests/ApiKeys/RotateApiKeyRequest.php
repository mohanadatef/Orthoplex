<?php

namespace App\Http\Requests\ApiKeys;

use Illuminate\Foundation\Http\FormRequest;

class RotateApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('api_keys.rotate') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
