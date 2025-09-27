<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => ['required','string','max:100'],
            'email' => ['required','email'],
            'password' => ['required','string','min:8','confirmed'],
            'org_name' => ['required','string','max:120'],
            'idempotency_key' => ['nullable','string','max:64']
        ];
    }
}
