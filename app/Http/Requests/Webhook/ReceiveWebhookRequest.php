<?php

namespace App\Http\Requests\Webhook;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'     => ['required','url'],
            'event'   => ['required','string'],
            'payload' => ['required','array'],
        ];
    }
}
