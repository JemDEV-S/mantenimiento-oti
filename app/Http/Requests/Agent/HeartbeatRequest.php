<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class HeartbeatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_version'      => ['required', 'string', 'max:20'],
            'last_ip'            => ['nullable', 'ip'],
            'last_snapshot_json' => ['nullable', 'array'],
        ];
    }
}
