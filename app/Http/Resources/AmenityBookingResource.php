<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AmenityBooking
 */
class AmenityBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->uuid,
            'amenity'             => new AmenityResource($this->whenLoaded('amenity')),
            'property'            => new PropertyResource($this->whenLoaded('property')),
            'booked_by'           => new UserResource($this->whenLoaded('booker')),
            'title'               => $this->title,
            'start_at'            => $this->start_at?->toIso8601String(),
            'end_at'              => $this->end_at?->toIso8601String(),
            'status'              => $this->status->value,
            'status_label'        => $this->status->label(),
            'notes'               => $this->notes,
            'cancelled_at'        => $this->cancelled_at?->toIso8601String(),
            'cancellation_reason' => $this->cancellation_reason,
            'created_at'          => $this->created_at?->toIso8601String(),
        ];
    }
}
