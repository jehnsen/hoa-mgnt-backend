<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\ClearanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClearanceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageFinancials() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $status = $this->input('status');

        return [
            'status'           => ['required', Rule::enum(ClearanceStatus::class)],
            'valid_until'      => [
                Rule::requiredIf($status === ClearanceStatus::Issued->value),
                'nullable', 'date', 'after:today',
            ],
            'rejection_reason' => [
                Rule::requiredIf($status === ClearanceStatus::Rejected->value),
                'nullable', 'string', 'max:2000',
            ],
        ];
    }
}
