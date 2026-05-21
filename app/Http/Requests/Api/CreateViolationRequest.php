<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageViolations() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id'  => ['required', 'string', 'exists:properties,uuid'],
            'title'        => ['required', 'string', 'min:5', 'max:200'],
            'description'  => ['required', 'string', 'min:20', 'max:5000'],
            'fine_amount'  => ['nullable', 'numeric', 'min:0', 'max:999999.99'],

            'evidence_images'               => ['nullable', 'array', 'max:10'],
            'evidence_images.*.path'        => ['required_with:evidence_images', 'string', 'max:500'],
            'evidence_images.*.lat'         => ['required_with:evidence_images', 'numeric', 'between:-90,90'],
            'evidence_images.*.lng'         => ['required_with:evidence_images', 'numeric', 'between:-180,180'],
            'evidence_images.*.captured_at' => ['required_with:evidence_images', 'date', 'before_or_equal:now'],
        ];
    }
}
