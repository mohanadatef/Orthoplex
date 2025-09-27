<?php

namespace App\Http\Requests\MagicLink;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMagicLink extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required','string'],
        ];
    }
}
