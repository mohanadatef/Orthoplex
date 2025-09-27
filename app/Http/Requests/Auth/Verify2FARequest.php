<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class Verify2FARequest extends FormRequest {
    public function rules(): array {
        return [
            'code' => ['required','string','size:6'],
            'backup_code' => ['nullable','string','size:10']
        ];
    }
}
