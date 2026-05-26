<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreViolationAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'notes'                         => ['required', 'string', 'min:20', 'max:5000'],

            'evidence_images'               => ['nullable', 'array', 'max:10'],
            'evidence_images.*.path'        => ['required_with:evidence_images', 'string', 'max:500'],
            'evidence_images.*.lat'         => ['required_with:evidence_images', 'numeric', 'between:-90,90'],
            'evidence_images.*.lng'         => ['required_with:evidence_images', 'numeric', 'between:-180,180'],
            'evidence_images.*.captured_at' => ['required_with:evidence_images', 'date', 'before_or_equal:now'],
        ];
    }
}
