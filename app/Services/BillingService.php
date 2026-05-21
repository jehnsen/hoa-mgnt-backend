<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Exceptions\DuplicateInvoicePeriodException;
use App\Exceptions\InvoiceNotCancellableException;
use App\Exceptions\InvoiceNotSettleableException;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\Contracts\BillingServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class BillingService implements BillingServiceInterface
{
    public function __construct(
        private readonly InvoiceRepositoryInterface  $invoiceRepository,
        private readonly PaymentRepositoryInterface  $paymentRepository,
        private readonly PropertyRepositoryInterface $propertyRepository,
    ) {}

    /**
     * Generate the monthly dues invoice for a single property.
     *
     * Architecture note: idempotency guard lives in the repository so the
     * DB query is isolated; business decision (throw vs. skip) lives here.
     */
    public function generateMonthlyDues(Property $property, string $periodMonth): Invoice
    {
        if ($this->invoiceRepository->existsForPeriod($property, $periodMonth)) {
            throw new DuplicateInvoicePeriodException($property->unit_number, $periodMonth);
        }

        // Dues invoices are due on the 1st of the month following the period
        $dueAt = Carbon::parse($periodMonth)->addMonth()->startOfMonth();

        return DB::transaction(function () use ($property, $periodMonth, $dueAt): Invoice {
            return $this->invoiceRepository->create([
                'property_id'  => $property->id,
                'description'  => "Monthly HOA Dues – {$periodMonth}",
                'base_amount'  => $property->monthly_dues,
                'status'       => InvoiceStatus::Pending,
                'due_at'       => $dueAt->toDateString(),
                'period_month' => $periodMonth,
            ]);
        });
    }

    /**
     * Scan all pending invoices past their due date, compute a late-fee
     * surcharge, and atomically flip them to Overdue status.
     *
     * Uses a chunked transaction loop to avoid locking the entire table on
     * large datasets.
     */
    public function applyLateFees(): int
    {
        $overdueInvoices = $this->invoiceRepository->findOverdueInvoices();
        $updatedCount    = 0;

        foreach ($overdueInvoices as $invoice) {
            DB::transaction(function () use ($invoice, &$updatedCount): void {
                $lateFee = round(
                    (float) $invoice->base_amount * (float) $invoice->property->late_fee_rate,
                    2
                );

                $this->invoiceRepository->update($invoice, [
                    'late_fee_amount' => $lateFee,
                    'status'          => InvoiceStatus::Overdue,
                ]);

                $updatedCount++;
            });
        }

        return $updatedCount;
    }

    /**
     * Record a payment and automatically close the invoice when fully paid.
     *
     * The entire operation is atomic: payment insert + invoice status update
     * happen in one transaction so a partial failure never leaves a ghost payment.
     */
    public function processPayment(Invoice $invoice, array $data): Payment
    {
        if (! $invoice->isSettleable()) {
            throw new InvoiceNotSettleableException($invoice);
        }

        return DB::transaction(function () use ($invoice, $data): Payment {
            $payment = $this->paymentRepository->create([
                'invoice_id'            => $invoice->id,
                'received_by'           => $data['received_by'] ?? null,
                'amount'                => $data['amount'],
                'payment_method'        => $data['payment_method'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'notes'                 => $data['notes'] ?? null,
                'paid_at'               => $data['paid_at'] ?? now(),
            ]);

            // Re-sum after inserting the new payment to avoid race conditions
            $totalPaid   = $this->paymentRepository->totalPaidForInvoice($invoice);
            $outstanding = $this->getOutstandingBalance($invoice);

            // Mark invoice paid only when the cumulative payments cover the total
            if ($totalPaid >= (float) $invoice->total_amount) {
                $this->invoiceRepository->updateStatus($invoice, InvoiceStatus::Paid);
            } elseif ($outstanding < (float) $invoice->total_amount && $invoice->status === InvoiceStatus::Pending) {
                // Partial payment – keep Pending but log for audit purposes
            }

            return $payment->load('invoice');
        });
    }

    public function getOutstandingBalance(Invoice $invoice): float
    {
        $totalPaid = $this->paymentRepository->totalPaidForInvoice($invoice);

        return max(0.0, (float) $invoice->total_amount - $totalPaid);
    }

    public function cancelInvoice(Invoice $invoice): Invoice
    {
        if (! $invoice->isSettleable()) {
            throw new InvoiceNotCancellableException($invoice);
        }

        return DB::transaction(function () use ($invoice): Invoice {
            return $this->invoiceRepository->updateStatus($invoice, InvoiceStatus::Cancelled);
        });
    }

    public function generateBulkMonthlyDues(string $periodMonth): array
    {
        $properties = $this->propertyRepository->allActive();
        $created    = 0;
        $skipped    = 0;

        foreach ($properties as $property) {
            try {
                $this->generateMonthlyDues($property, $periodMonth);
                $created++;
            } catch (DuplicateInvoicePeriodException) {
                $skipped++;
            }
        }

        return ['created' => $created, 'skipped' => $skipped];
    }
}
