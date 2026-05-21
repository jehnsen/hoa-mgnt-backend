<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Payment
 */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->uuid,
            'invoice_id'            => $this->invoice?->uuid,
            'amount'                => $this->amount,
            'payment_method'        => $this->payment_method->value,
            'payment_method_label'  => $this->payment_method->label(),
            'transaction_reference' => $this->transaction_reference,
            'notes'                 => $this->notes,
            'received_by'           => new UserResource($this->whenLoaded('receiver')),
            'paid_at'               => $this->paid_at?->toIso8601String(),
            'created_at'            => $this->created_at?->toIso8601String(),
        ];
    }
}
