<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates payment records for every invoice already marked as Paid.
 *
 * Payment method distribution (mirrors real PH HOA collection patterns):
 *  - GCash / online transfer  → 45%  (most residents pay via GCash)
 *  - Bank transfer (BancNet)  → 30%  (Landbank, BDO, Metrobank)
 *  - Cash over-the-counter    → 15%  (paid at the HOA office)
 *  - Check                    → 10%  (mostly from corporate lot owners)
 *
 * Reference number formats:
 *  - GCash      : "GCH-" + 13-digit YYYYMMDDxxxxx
 *  - BancNet    : "BN-"  + 14-digit YYYYMMDDxxxxxx
 *  - Cash       : "CR-"  + year + sequential number
 *  - Check      : "CHK-" + bank code + check number
 */
class PaymentSeeder extends Seeder
{
    private int $cashReceiptCounter = 1;

    public function run(): void
    {
        $boardMemberId = User::where('email', 'lourdes.santos@springdale-hoa.ph')->value('id');
        $paidInvoices  = Invoice::where('status', InvoiceStatus::Paid)
                                ->with('property')
                                ->orderBy('paid_at')
                                ->get();

        foreach ($paidInvoices as $invoice) {
            $method    = $this->pickPaymentMethod($invoice->property->unit_number);
            $reference = $this->generateReference($method, $invoice->paid_at?->toDateTimeString());
            $notes     = $this->buildNotes($method, $invoice->property->unit_number);

            Payment::create([
                'invoice_id'            => $invoice->id,
                'received_by'           => $boardMemberId,
                'amount'                => $invoice->total_amount,
                'payment_method'        => $method,
                'transaction_reference' => $reference,
                'notes'                 => $notes,
                'paid_at'               => $invoice->paid_at,
            ]);
        }

        $this->command->info('  ✔ Seeded ' . Payment::count() . ' payment records for all Paid invoices.');
    }

    /**
     * Assign a consistent payment method per unit so each household
     * has a realistic, stable payment habit.
     */
    private function pickPaymentMethod(string $unitNumber): PaymentMethod
    {
        // Assign based on unit to keep the same resident's payment habit consistent
        return match (true) {
            in_array($unitNumber, ['B1-L01', 'B1-L02', 'B2-L01', 'B2-L04', 'B3-L01', 'B4-L01'], true)
                => PaymentMethod::Online,           // GCash users

            in_array($unitNumber, ['B1-L04', 'B2-L02', 'B3-L02', 'B5-L01'], true)
                => PaymentMethod::BankTransfer,     // BancNet / IBT users

            in_array($unitNumber, ['B2-L03', 'B4-L02', 'B5-L02'], true)
                => PaymentMethod::Cash,             // over-the-counter cash payers

            in_array($unitNumber, ['B1-L03', 'B1-L05', 'B4-L03'], true)
                => PaymentMethod::Check,            // check payers

            default => PaymentMethod::Online,
        };
    }

    /**
     * Generate a realistic Philippine payment reference number.
     */
    private function generateReference(PaymentMethod $method, ?string $paidAt): string
    {
        $dateKey = $paidAt ? date('Ymd', strtotime($paidAt)) : date('Ymd');
        $rand5   = str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $rand6   = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        return match ($method) {
            PaymentMethod::Online       => "GCH-{$dateKey}{$rand5}",
            PaymentMethod::BankTransfer => "BN-{$dateKey}{$rand6}",
            PaymentMethod::Cash         => 'CR-2026-' . str_pad((string) $this->cashReceiptCounter++, 4, '0', STR_PAD_LEFT),
            PaymentMethod::Check        => "CHK-BDO-" . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
            default                     => "REF-{$dateKey}-{$rand5}",
        };
    }

    private function buildNotes(PaymentMethod $method, string $unitNumber): string
    {
        return match ($method) {
            PaymentMethod::Online =>
                "GCash payment received. Sender: {$unitNumber} resident. Auto-posted via HOA GCash number 0917-100-2001.",
            PaymentMethod::BankTransfer =>
                "BDO/Landbank fund transfer confirmed. Deposited to HOA account (Metrobank CA No. 042-7-04231234-8). Unit: {$unitNumber}.",
            PaymentMethod::Cash =>
                "Cash payment received at HOA office. Official receipt issued. Unit: {$unitNumber}.",
            PaymentMethod::Check =>
                "Check payment cleared. BDO check deposited to HOA Metrobank account. Unit: {$unitNumber}.",
            default => "Payment received for {$unitNumber}.",
        };
    }
}
