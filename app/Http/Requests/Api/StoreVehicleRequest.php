<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $boardPlus = $this->user()?->canManageFinancials() ?? false;

        return [
            'property_id'      => ['required', 'string', 'exists:properties,uuid'],
            'plate_number'     => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'type'             => ['required', Rule::enum(VehicleType::class)],
            'make'             => ['required', 'string', 'max:100'],
            'model'            => ['required', 'string', 'max:100'],
            'color'            => ['required', 'string', 'max:50'],
            'year'             => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'sticker_number'   => $boardPlus ? ['nullable', 'string', 'max:50', 'unique:vehicles,sticker_number'] : ['prohibited'],
            'gate_pass_number' => $boardPlus ? ['nullable', 'string', 'max:50', 'unique:vehicles,gate_pass_number'] : ['prohibited'],
        ];
    }
}
