<?php

namespace App\Http\Requests\Org;

use Illuminate\Foundation\Http\FormRequest;

class ProvisioningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // تحكم بالـ Middleware
    }

    public function rules(): array
    {
        return [
            'org_name' => ['required','string','max:255'],
            'users'    => ['required','array'],
            'users.*.email' => ['required','email'],
            'users.*.role'  => ['required','in:owner,admin,member,auditor'],
        ];
    }
}
