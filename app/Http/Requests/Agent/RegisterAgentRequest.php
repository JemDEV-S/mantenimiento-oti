<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autenticación se controla via API token en el middleware
    }

    public function rules(): array
    {
        return [
            'asset_internal_code' => ['required', 'string', 'exists:assets,internal_code'],
            'hostname'            => ['required', 'string', 'max:200'],
            'serial_number'       => ['required', 'string', 'max:100'],
            'device_model'        => ['nullable', 'string', 'max:100'],
            'operating_system'    => ['nullable', 'string', 'max:150'],
            'agent_version'       => ['required', 'string', 'max:20'],
            'last_ip'             => ['nullable', 'ip'],
            'last_snapshot_json'  => ['nullable', 'array'],
        ];
    }
}
