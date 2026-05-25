<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'                  => ['sometimes', 'string', 'min:2', 'max:255'],
            'phone'                 => ['sometimes', 'nullable', 'string', 'max:30'],
            'current_password'      => ['required_with:password', 'string'],
            'password'              => ['sometimes', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => ['required_with:password', 'string'],
        ];
    }
}
