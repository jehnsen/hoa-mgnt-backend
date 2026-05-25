<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\AnnouncementAudience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'min:5', 'max:200'],
            'body'         => ['required', 'string', 'min:10'],
            'audience'     => ['required', Rule::enum(AnnouncementAudience::class)],
            'is_pinned'    => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'expires_at'   => ['nullable', 'date', 'after:published_at'],
        ];
    }
}
