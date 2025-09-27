<?php
namespace App\Http\Requests\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => ['required','string','max:100'],
            'email' => ['required','email'],
            'password' => ['required','string','min:8'],
            'roles' => ['nullable','array']
        ];
    }
}
