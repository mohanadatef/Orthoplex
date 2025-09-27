<?php

namespace App\Http\Requests\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.invite') ?? false;
    }

    public function rules(): array
    {
        return [
            'email' => ['required','email'],
            'role'  => ['required','in:owner,admin,member,auditor'],
            'org_id'=> ['required','integer','exists:orgs,id'],
        ];
    }
}
