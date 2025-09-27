<?php

namespace App\Http\Requests\Org;

use Illuminate\Foundation\Http\FormRequest;

class OrgStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.invite') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required','string','max:255'],
        ];
    }
}
