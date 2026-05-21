<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(private readonly Payment $model) {}

    public function findById(int $id): ?Payment
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Payment
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['invoice', 'receiver'])
                           ->first();
    }

    public function findForInvoice(Invoice $invoice): Collection
    {
        return $this->model->newQuery()
                           ->where('invoice_id', $invoice->id)
                           ->orderBy('paid_at')
                           ->get();
    }

    public function totalPaidForInvoice(Invoice $invoice): float
    {
        return (float) $this->model->newQuery()
                                   ->where('invoice_id', $invoice->id)
                                   ->sum('amount');
    }

    public function create(array $data): Payment
    {
        return $this->model->newQuery()->create($data);
    }
}
