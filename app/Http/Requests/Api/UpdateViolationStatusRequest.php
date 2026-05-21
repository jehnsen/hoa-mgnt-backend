<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\ViolationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateViolationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageViolations() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ViolationStatus::class)],
        ];
    }
}
