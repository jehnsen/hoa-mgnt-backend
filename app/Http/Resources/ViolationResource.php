<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Violation
 */
class ViolationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->uuid,
            'property'         => new PropertyResource($this->whenLoaded('property')),
            'reporter'         => new UserResource($this->whenLoaded('reporter')),
            'title'            => $this->title,
            'description'      => $this->description,
            'category'         => $this->category?->value,
            'category_label'   => $this->category?->label(),
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'fine_amount'      => $this->fine_amount,
            'fine_invoice'     => new InvoiceResource($this->whenLoaded('fineInvoice')),
            // Evidence images carry geotagging metadata inline
            'evidence_images'  => $this->evidence_images ?? [],
            'issued_at'        => $this->issued_at?->toIso8601String(),
            'resolved_at'      => $this->resolved_at?->toIso8601String(),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
