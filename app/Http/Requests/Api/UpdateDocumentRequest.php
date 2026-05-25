<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\DocumentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'string', 'min:3', 'max:200'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:2000'],
            'category'     => ['sometimes', Rule::enum(DocumentCategory::class)],
            'is_public'    => ['sometimes', 'boolean'],
            'published_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
