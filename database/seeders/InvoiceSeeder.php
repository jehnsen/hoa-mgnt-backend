<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Database\Seeder;

/**
 * Seeds 6 months of HOA dues invoices (Dec 2025 – May 2026) for all 20 units.
 *
 * Payment Profile Categories (assigned per unit):
 *
 *  PROFILE A – "Prompt Payer" (12 units)
 *    Dec 2025 → Jan 2026: Paid on time each period
 *    Apr 2026            : Also paid (settled before May 1 due date)
 *    May 2026            : Pending (current billing month)
 *
 *  PROFILE B – "Occasional Late Payer" (5 units)
 *    Dec 2025 – Feb 2026 : Paid
 *    Mar 2026            : Overdue with 2% late fee (missed Apr 1 due date)
 *    Apr 2026            : Overdue with 2% late fee (past May 1 due date)
 *    May 2026            : Pending
 *
 *  PROFILE C – "Chronic Delinquent / Vacant" (3 units: B3-L04, B4-L04, B5-L03)
 *    Dec 2025 – Mar 2026 : Overdue with accumulated late fees
 *    Apr 2026            : Overdue
 *    May 2026            : Pending (HOA still bills the property owner)
 *
 * Billing schedule: dues cover the stated month; due date is the 1st of the next month.
 */
class InvoiceSeeder extends Seeder
{
    /** @var array<int, array{period: string, due_at: string, label: string}> */
    private array $billingPeriods = [
        ['period' => '2025-12-01', 'due_at' => '2026-01-01', 'label' => 'December 2025'],
        ['period' => '2026-01-01', 'due_at' => '2026-02-01', 'label' => 'January 2026'],
        ['period' => '2026-02-01', 'due_at' => '2026-03-01', 'label' => 'February 2026'],
        ['period' => '2026-03-01', 'due_at' => '2026-04-01', 'label' => 'March 2026'],
        ['period' => '2026-04-01', 'due_at' => '2026-05-01', 'label' => 'April 2026'],
        ['period' => '2026-05-01', 'due_at' => '2026-06-01', 'label' => 'May 2026'],
    ];

    /** @var array<string, string> unit_number → payment profile */
    private array $paymentProfiles = [
        // Profile A – Prompt payers
        'B1-L01' => 'A', 'B1-L02' => 'A', 'B2-L01' => 'A', 'B2-L02' => 'A',
        'B2-L04' => 'A', 'B3-L01' => 'A', 'B3-L02' => 'A', 'B4-L01' => 'A',
        'B4-L02' => 'A', 'B5-L01' => 'A', 'B5-L02' => 'A', 'B1-L04' => 'A',

        // Profile B – Occasional late payers
        'B1-L03' => 'B', 'B1-L05' => 'B', 'B2-L03' => 'B',
        'B3-L03' => 'B', 'B4-L03' => 'B',

        // Profile C – Chronic delinquents / vacant units
        'B3-L04' => 'C', 'B4-L04' => 'C', 'B5-L03' => 'C',
    ];

    public function run(): void
    {
        $properties = Property::all()->keyBy('unit_number');
        $created    = 0;

        foreach ($properties as $unitNumber => $property) {
            $profile = $this->paymentProfiles[$unitNumber] ?? 'A';

            foreach ($this->billingPeriods as $billing) {
                [$status, $lateFee, $paidAt] = $this->resolveStatusForProfile(
                    $profile,
                    $billing['period'],
                    (float) $property->monthly_dues,
                    (float) $property->late_fee_rate,
                );

                Invoice::create([
                    'property_id'     => $property->id,
                    'description'     => "HOA Monthly Dues – {$billing['label']}",
                    'base_amount'     => $property->monthly_dues,
                    'late_fee_amount' => $lateFee,
                    'status'          => $status,
                    'due_at'          => $billing['due_at'],
                    'period_month'    => $billing['period'],
                    'paid_at'         => $paidAt,
                ]);

                $created++;
            }
        }

        $counts = [
            InvoiceStatus::Paid->value    => Invoice::where('status', InvoiceStatus::Paid)->count(),
            InvoiceStatus::Overdue->value => Invoice::where('status', InvoiceStatus::Overdue)->count(),
            InvoiceStatus::Pending->value => Invoice::where('status', InvoiceStatus::Pending)->count(),
        ];

        $this->command->info(
            "  ✔ Seeded {$created} invoices — " .
            "Paid: {$counts['paid']}, Overdue: {$counts['overdue']}, Pending: {$counts['pending']}."
        );
    }

    /**
     * Determine invoice status, late fee, and paid_at timestamp
     * based on the unit's payment profile and the billing period.
     *
     * @return array{InvoiceStatus, float, string|null}
     */
    private function resolveStatusForProfile(
        string $profile,
        string $period,
        float  $baseDues,
        float  $lateFeeRate,
    ): array {
        $lateFee = 0.00;
        $paidAt  = null;

        return match (true) {
            // ── Profile A: Prompt payers ───────────────────────────────────────
            $profile === 'A' && $period === '2026-05-01' => [
                InvoiceStatus::Pending, $lateFee, $paidAt,
            ],
            $profile === 'A' => [
                InvoiceStatus::Paid,
                $lateFee,
                // Paid 3–10 days before the due date (early, responsible residents)
                $this->paidBeforeDue($period, daysBeforeDue: random_int(3, 10)),
            ],

            // ── Profile B: Occasional late payers ─────────────────────────────
            $profile === 'B' && $period === '2026-05-01' => [
                InvoiceStatus::Pending, $lateFee, $paidAt,
            ],
            $profile === 'B' && in_array($period, ['2026-03-01', '2026-04-01'], true) => [
                InvoiceStatus::Overdue,
                round($baseDues * $lateFeeRate, 2),
                $paidAt,
            ],
            $profile === 'B' => [
                InvoiceStatus::Paid,
                $lateFee,
                // Paid 1–5 days before due date (cutting it close)
                $this->paidBeforeDue($period, daysBeforeDue: random_int(1, 5)),
            ],

            // ── Profile C: Chronic delinquents / vacant ────────────────────────
            $profile === 'C' && $period === '2026-05-01' => [
                InvoiceStatus::Pending, $lateFee, $paidAt,
            ],
            $profile === 'C' => [
                InvoiceStatus::Overdue,
                round($baseDues * $lateFeeRate, 2),
                $paidAt,
            ],

            // Fallback
            default => [InvoiceStatus::Pending, $lateFee, $paidAt],
        };
    }

    /**
     * Compute a realistic paid_at datetime: X days before the due date.
     * Due date is always the 1st of the month following the period.
     */
    private function paidBeforeDue(string $period, int $daysBeforeDue): string
    {
        // Due date = period month + 1 month (e.g. Dec 2025 period → Jan 1 2026 due)
        return date(
            'Y-m-d H:i:s',
            strtotime("{$period} +1 month -{$daysBeforeDue} days")
        );
    }
}
