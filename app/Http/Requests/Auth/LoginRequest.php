<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {
    public function rules(): array {
        return [
            'email' => ['required','email'],
            'password' => ['required','string','min:8'],
            'idempotency_key' => ['nullable','string','max:64']
        ];
    }
}
