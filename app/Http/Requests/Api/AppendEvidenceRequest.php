<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AppendEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageViolations() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'evidence_images'               => ['required', 'array', 'min:1', 'max:10'],
            'evidence_images.*.path'        => ['required', 'string', 'max:500'],
            'evidence_images.*.lat'         => ['required', 'numeric', 'between:-90,90'],
            'evidence_images.*.lng'         => ['required', 'numeric', 'between:-180,180'],
            'evidence_images.*.captured_at' => ['required', 'date', 'before_or_equal:now'],
        ];
    }
}
