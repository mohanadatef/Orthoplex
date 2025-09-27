<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class MagicLinkRequest extends FormRequest {
    public function rules(): array {
        return ['email' => ['required','email']];
    }
}
