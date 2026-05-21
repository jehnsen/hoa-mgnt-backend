<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Invoice
 */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->uuid,
            'property'        => new PropertyResource($this->whenLoaded('property')),
            'description'     => $this->description,
            'base_amount'     => $this->base_amount,
            'late_fee_amount' => $this->late_fee_amount,
            'total_amount'    => $this->total_amount,
            'status'          => $this->status->value,
            'status_label'    => $this->status->label(),
            'due_at'          => $this->due_at?->toDateString(),
            'period_month'    => $this->period_month?->format('Y-m'),
            'paid_at'         => $this->paid_at?->toIso8601String(),
            'payments'        => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
