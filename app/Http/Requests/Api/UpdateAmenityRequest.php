<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmenityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'string', 'min:2', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'location'    => ['sometimes', 'nullable', 'string', 'max:200'],
            'capacity'    => ['sometimes', 'nullable', 'integer', 'min:1', 'max:9999'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
