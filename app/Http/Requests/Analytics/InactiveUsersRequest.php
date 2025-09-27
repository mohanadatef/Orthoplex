<?php

namespace App\Http\Requests\Analytics;

use Illuminate\Foundation\Http\FormRequest;

class InactiveUsersRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user()?->can('analytics.read') ?? false;
    }


    public function rules(): array
    {
        return [
            'days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ];
    }

    public function messages(): array
    {
        return [
            'days.integer' => __('analytics.days_integer'),
            'days.min'     => __('analytics.days_min'),
            'days.max'     => __('analytics.days_max'),
        ];
    }
}
