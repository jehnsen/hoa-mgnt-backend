<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'company_name'   => ['sometimes', 'string', 'max:255'],
            'service_type'   => ['sometimes', 'string', 'max:100'],
            'license_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_active'      => ['sometimes', 'boolean'],
            'notes'          => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
