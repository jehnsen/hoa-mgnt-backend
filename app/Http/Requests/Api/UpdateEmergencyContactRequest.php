<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'string', 'max:150'],
            'relationship' => ['sometimes', 'string', 'max:100'],
            'phone'        => ['sometimes', 'string', 'max:30'],
            'email'        => ['sometimes', 'nullable', 'email', 'max:255'],
            'is_primary'   => ['sometimes', 'boolean'],
        ];
    }
}
