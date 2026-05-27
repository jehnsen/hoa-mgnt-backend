<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UtilityMeterReading */
class UtilityMeterReadingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->uuid,
            'property'         => new PropertyResource($this->whenLoaded('property')),
            'utility_type'     => $this->utility_type->value,
            'utility_label'    => $this->utility_type->label(),
            'meter_number'     => $this->meter_number,
            'previous_reading' => (float) $this->previous_reading,
            'current_reading'  => (float) $this->current_reading,
            'consumption'      => (float) $this->consumption,
            'reading_date'     => $this->reading_date?->toDateString(),
            'rate_per_unit'    => (float) $this->rate_per_unit,
            'fixed_charge'     => (float) $this->fixed_charge,
            'total_amount'     => $this->totalAmount(),
            'notes'            => $this->notes,
            'reader'           => new UserResource($this->whenLoaded('reader')),
            'invoice_id'       => $this->invoice?->uuid,
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
