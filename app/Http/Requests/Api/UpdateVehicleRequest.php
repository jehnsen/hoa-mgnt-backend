<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $uuid      = $this->route('uuid');
        $boardPlus = $this->user()?->canManageFinancials() ?? false;

        return [
            'plate_number'     => ['sometimes', 'string', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($uuid, 'uuid')],
            'type'             => ['sometimes', Rule::enum(VehicleType::class)],
            'make'             => ['sometimes', 'string', 'max:100'],
            'model'            => ['sometimes', 'string', 'max:100'],
            'color'            => ['sometimes', 'string', 'max:50'],
            'year'             => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'sticker_number'   => $boardPlus ? ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('vehicles', 'sticker_number')->ignore($uuid, 'uuid')] : ['prohibited'],
            'gate_pass_number' => $boardPlus ? ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('vehicles', 'gate_pass_number')->ignore($uuid, 'uuid')] : ['prohibited'],
            'is_active'        => $boardPlus ? ['sometimes', 'boolean'] : ['prohibited'],
        ];
    }
}
