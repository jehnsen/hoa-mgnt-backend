<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\MaintenanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenanceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status'           => ['required', Rule::enum(MaintenanceStatus::class)],
            'resolution_notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'assigned_to'      => ['sometimes', 'nullable', 'string', 'exists:users,uuid'],
        ];
    }
}
