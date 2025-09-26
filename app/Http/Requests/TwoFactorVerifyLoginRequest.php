<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwoFactorVerifyLoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'code' => 'required|string'
        ];
    }
}
