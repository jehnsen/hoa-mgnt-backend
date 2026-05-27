<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenanceFrequency;
use App\Enums\MaintenancePriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecurringMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id'        => ['sometimes', 'nullable', 'string', 'exists:properties,uuid'],
            'assigned_to'        => ['sometimes', 'nullable', 'string', 'exists:users,uuid'],
            'category'           => ['sometimes', Rule::enum(MaintenanceCategory::class)],
            'title'              => ['sometimes', 'string', 'max:255'],
            'description'        => ['sometimes', 'string', 'max:5000'],
            'priority'           => ['sometimes', Rule::enum(MaintenancePriority::class)],
            'frequency'          => ['sometimes', Rule::enum(MaintenanceFrequency::class)],
            'frequency_interval' => ['sometimes', 'integer', 'min:1', 'max:52'],
            'estimated_cost'     => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'next_due_at'        => ['sometimes', 'date'],
            'is_active'          => ['sometimes', 'boolean'],
        ];
    }
}
