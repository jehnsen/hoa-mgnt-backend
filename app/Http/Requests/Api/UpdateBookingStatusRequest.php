<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status'               => ['required', Rule::enum(BookingStatus::class)],
            'cancellation_reason'  => ['nullable', 'string', 'max:500', 'required_if:status,cancelled'],
        ];
    }
}
