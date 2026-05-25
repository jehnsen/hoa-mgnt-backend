<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateAmenityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location'    => ['nullable', 'string', 'max:200'],
            'capacity'    => ['nullable', 'integer', 'min:1', 'max:9999'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
