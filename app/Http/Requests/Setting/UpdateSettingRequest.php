<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('setting.edit');
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'string'],
        ];
    }
}
