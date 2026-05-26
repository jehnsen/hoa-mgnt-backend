<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\BoardPositionTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBoardPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_uuid'  => ['required', 'string', 'exists:users,uuid'],
            'position'   => ['required', new Enum(BoardPositionTitle::class)],
            'term_start' => ['required', 'date'],
            'term_end'   => ['nullable', 'date', 'after:term_start'],
        ];
    }
}
