<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $periodMonth = now()->startOfMonth()->toDateString();

        return [
            'property_id'     => Property::factory(),
            'type'            => InvoiceType::MonthlyDues->value,
            'description'     => 'Monthly HOA Dues – ' . now()->format('Y-m'),
            'base_amount'     => 2500.00,
            'late_fee_amount' => 0.00,
            'status'          => InvoiceStatus::Pending->value,
            'due_at'          => now()->addMonth()->startOfMonth()->toDateString(),
            'period_month'    => $periodMonth,
            'paid_at'         => null,
        ];
    }

    public function overdue(): static
    {
        return $this->state([
            'status'          => InvoiceStatus::Overdue->value,
            'due_at'          => now()->subDays(10)->toDateString(),
            'late_fee_amount' => 125.00,
        ]);
    }

    public function paid(): static
    {
        return $this->state([
            'status'  => InvoiceStatus::Paid->value,
            'paid_at' => now()->toDateTimeString(),
        ]);
    }

    public function forPeriod(string $yearMonth): static
    {
        return $this->state([
            'description'  => "Monthly HOA Dues – {$yearMonth}",
            'period_month' => $yearMonth . '-01',
            'due_at'       => now()->parse($yearMonth)->addMonth()->startOfMonth()->toDateString(),
        ]);
    }
}
