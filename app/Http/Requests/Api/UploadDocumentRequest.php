<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\DocumentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'min:3', 'max:200'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'category'     => ['required', Rule::enum(DocumentCategory::class)],
            'file_path'    => ['required', 'string', 'max:500'],
            'file_name'    => ['required', 'string', 'max:255'],
            'file_size'    => ['required', 'integer', 'min:1'],
            'mime_type'    => ['required', 'string', 'max:100'],
            'is_public'    => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
