<?php
namespace App\Http\Requests\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => ['sometimes','string','max:100'],
            'email' => ['sometimes','email'],
            'password' => ['sometimes','string','min:8'],
            'roles' => ['nullable','array'],
            'version' => ['sometimes','integer']
        ];
    }
}
