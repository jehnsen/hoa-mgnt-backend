<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;

    public function findByUuid(string $uuid): ?Payment;

    /** @return Collection<int, Payment> */
    public function findForInvoice(Invoice $invoice): Collection;

    /** Sum of all payments made against a given invoice */
    public function totalPaidForInvoice(Invoice $invoice): float;

    /** @param array<string, mixed> $data */
    public function create(array $data): Payment;
}
