<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateVendorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_id'        => ['required', 'string', 'exists:users,uuid'],
            'company_name'   => ['required', 'string', 'max:255'],
            'service_type'   => ['required', 'string', 'max:100'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ];
    }
}
