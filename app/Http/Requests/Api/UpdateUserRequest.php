<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Route parameter is now a UUID string; exclude by uuid column for uniqueness check
        $userUuid = $this->route('user');

        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'max:255', "unique:users,email,{$userUuid},uuid"],
            'password' => ['sometimes', Password::min(8)->mixedCase()->numbers()],
            'role'     => ['sometimes', new Enum(UserRole::class)],
            'phone'    => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }
}
