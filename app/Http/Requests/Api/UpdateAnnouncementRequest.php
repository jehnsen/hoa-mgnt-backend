<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\AnnouncementAudience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'string', 'min:5', 'max:200'],
            'body'         => ['sometimes', 'string', 'min:10'],
            'audience'     => ['sometimes', Rule::enum(AnnouncementAudience::class)],
            'is_pinned'    => ['sometimes', 'boolean'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'expires_at'   => ['sometimes', 'nullable', 'date', 'after:published_at'],
        ];
    }
}
