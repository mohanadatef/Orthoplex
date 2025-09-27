<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter'  => ['nullable','string','max:2000'], // RSQL-like
            'fields'  => ['nullable','string','max:1000'],
            'include' => ['nullable','string','max:1000'],
            'cursor'  => ['nullable','string'],
            'per_page'=> ['nullable','integer','min:1','max:100'],
        ];
    }
}
