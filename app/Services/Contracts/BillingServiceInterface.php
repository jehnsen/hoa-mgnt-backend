<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Exceptions\InvoiceNotCancellableException;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;

interface BillingServiceInterface
{
    /**
     * Create the monthly dues invoice for a property.
     * Idempotent: throws if an invoice for the given period already exists.
     */
    public function generateMonthlyDues(Property $property, string $periodMonth): Invoice;

    /**
     * Apply a late-fee surcharge to any pending invoices whose due_at has passed.
     * Runs in a single transaction; returns the number of invoices updated.
     */
    public function applyLateFees(): int;

    /**
     * Record a payment against an invoice.
     * Atomically updates the invoice status to Paid when the payment satisfies
     * the full outstanding balance.
     *
     * @param array<string, mixed> $data
     */
    public function processPayment(Invoice $invoice, array $data): Payment;

    /**
     * Return the outstanding balance on an invoice (total - sum of payments).
     */
    public function getOutstandingBalance(Invoice $invoice): float;

    /**
     * Cancel a pending or overdue invoice.
     *
     * @throws InvoiceNotCancellableException when the invoice is already paid or cancelled
     */
    public function cancelInvoice(Invoice $invoice): Invoice;

    /**
     * Generate monthly dues invoices for every active property for the given period.
     * Already-invoiced properties are skipped (idempotent).
     *
     * @return array{created: int, skipped: int}
     */
    public function generateBulkMonthlyDues(string $periodMonth): array;
}
