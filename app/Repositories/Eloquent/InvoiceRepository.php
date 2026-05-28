<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use App\Models\Property;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(private readonly Invoice $model) {}

    public function findById(int $id): ?Invoice
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Invoice
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'payments'])
                           ->first();
    }

    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->where('property_id', $property->id)
                           ->with('payments')
                           ->orderByDesc('due_at')
                           ->paginate($perPage);
    }

    public function findOverdueInvoices(): Collection
    {
        // Select only invoices that are Pending AND whose due_at is in the past
        return $this->model->newQuery()
                           ->where('status', InvoiceStatus::Pending)
                           ->where('due_at', '<', Carbon::today())
                           ->with('property')
                           ->get();
    }

    public function existsForPeriod(Property $property, string $periodMonth): bool
    {
        // Use a YYYY-MM prefix LIKE to match regardless of whether the stored value
        // is 'Y-m-d' (MySQL) or 'Y-m-d H:i:s' (SQLite Eloquent date cast).
        $prefix = substr($periodMonth, 0, 7);

        return $this->model->newQuery()
                           ->where('property_id', $property->id)
                           ->where('period_month', 'LIKE', $prefix . '%')
                           ->whereNot('status', InvoiceStatus::Cancelled)
                           ->exists();
    }

    public function countConsecutiveOverdueMonthlyDues(int $propertyId): int
    {
        $invoices = $this->model->newQuery()
                                ->where('property_id', $propertyId)
                                ->where('type', InvoiceType::MonthlyDues)
                                ->whereNull('deleted_at')
                                ->orderByDesc('period_month')
                                ->limit(10)
                                ->get(['status', 'period_month']);

        $count = 0;
        foreach ($invoices as $invoice) {
            if ($invoice->status === InvoiceStatus::Overdue) {
                $count++;
            } else {
                break;
            }
        }

        return $count;
    }

    public function create(array $data): Invoice
    {
        return $this->model->newQuery()->create($data);
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        $invoice->status = $status;

        if ($status === InvoiceStatus::Paid) {
            $invoice->paid_at = now();
        }

        $invoice->save();

        return $invoice->refresh();
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->fill($data)->save();

        return $invoice->refresh();
    }
}
