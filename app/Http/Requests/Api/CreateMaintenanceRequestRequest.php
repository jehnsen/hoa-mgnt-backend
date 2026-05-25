<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMaintenanceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'string', 'exists:properties,uuid'],
            'category'    => ['required', Rule::enum(MaintenanceCategory::class)],
            'title'       => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'priority'    => ['sometimes', Rule::enum(MaintenancePriority::class)],
        ];
    }
}
