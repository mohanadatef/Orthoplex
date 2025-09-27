<?php

namespace App\Http\Requests\Org;

use Illuminate\Foundation\Http\FormRequest;

class OrgUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'           => ['sometimes','string','max:255'],
            'webhook_url'    => ['nullable','url'],
            'webhook_secret' => ['nullable','string','max:255'],
        ];
    }
}
