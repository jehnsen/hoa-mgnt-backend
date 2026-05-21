<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;

    public function findByUuid(string $uuid): ?Invoice;

    /** @return LengthAwarePaginator<Invoice> */
    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator;

    /** Find all invoices past their due_at date that are still Pending */
    /** @return Collection<int, Invoice> */
    public function findOverdueInvoices(): Collection;

    /** Check whether an invoice for this property + period already exists (prevents duplicates) */
    public function existsForPeriod(Property $property, string $periodMonth): bool;

    /** @param array<string, mixed> $data */
    public function create(array $data): Invoice;

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice;

    /** @param array<string, mixed> $data */
    public function update(Invoice $invoice, array $data): Invoice;
}
