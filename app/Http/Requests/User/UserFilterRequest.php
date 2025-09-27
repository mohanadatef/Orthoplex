<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.read') ?? false;
    }

    public function rules(): array
    {
        return [
            'filter'  => ['nullable','string'],
            'include' => ['nullable','string'],
            'fields'  => ['nullable','string'],
        ];
    }
}
