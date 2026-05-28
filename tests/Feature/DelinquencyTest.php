<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use App\Models\Property;
use App\Services\Contracts\DelinquencyServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DelinquencyTest extends TestCase
{
    use RefreshDatabase;

    private function makeOverdueDuesInvoice(Property $property, string $periodMonth): Invoice
    {
        return Invoice::factory()->overdue()->create([
            'property_id'  => $property->id,
            'type'         => InvoiceType::MonthlyDues->value,
            'period_month' => $periodMonth . '-01',
        ]);
    }

    // ── Delinquency flagging ───────────────────────────────────────────────────

    public function test_property_with_three_consecutive_overdue_invoices_is_flagged(): void
    {
        $property = Property::factory()->create(['is_delinquent' => false]);

        $this->makeOverdueDuesInvoice($property, '2026-01');
        $this->makeOverdueDuesInvoice($property, '2026-02');
        $this->makeOverdueDuesInvoice($property, '2026-03');

        $service = app(DelinquencyServiceInterface::class);
        $result  = $service->runEscalation();

        $this->assertSame(1, $result['flagged']);
        $this->assertSame(0, $result['cleared']);

        $this->assertDatabaseHas('properties', [
            'id'           => $property->id,
            'is_delinquent' => 1,
        ]);
    }

    public function test_property_with_only_two_overdue_invoices_is_not_flagged(): void
    {
        $property = Property::factory()->create(['is_delinquent' => false]);

        $this->makeOverdueDuesInvoice($property, '2026-02');
        $this->makeOverdueDuesInvoice($property, '2026-03');

        $service = app(DelinquencyServiceInterface::class);
        $result  = $service->runEscalation();

        $this->assertSame(0, $result['flagged']);

        $this->assertDatabaseHas('properties', [
            'id'           => $property->id,
            'is_delinquent' => 0,
        ]);
    }

    public function test_gap_in_overdue_streak_resets_count(): void
    {
        $property = Property::factory()->create(['is_delinquent' => false]);

        // Jan overdue, Feb paid, Mar-Apr overdue — streak of 2, not 3
        $this->makeOverdueDuesInvoice($property, '2026-01');

        Invoice::factory()->paid()->create([
            'property_id'  => $property->id,
            'type'         => InvoiceType::MonthlyDues->value,
            'period_month' => '2026-02-01',
        ]);

        $this->makeOverdueDuesInvoice($property, '2026-03');
        $this->makeOverdueDuesInvoice($property, '2026-04');

        $service = app(DelinquencyServiceInterface::class);
        $result  = $service->runEscalation();

        $this->assertSame(0, $result['flagged']);
    }

    // ── Delinquency clearing ──────────────────────────────────────────────────

    public function test_previously_delinquent_property_is_cleared_when_not_overdue(): void
    {
        $property = Property::factory()->delinquent()->create();

        // No overdue invoices — streak is 0
        $service = app(DelinquencyServiceInterface::class);
        $result  = $service->runEscalation();

        $this->assertSame(0, $result['flagged']);
        $this->assertSame(1, $result['cleared']);

        $this->assertDatabaseHas('properties', [
            'id'           => $property->id,
            'is_delinquent' => 0,
        ]);
    }

    public function test_already_delinquent_property_is_not_double_counted(): void
    {
        $property = Property::factory()->delinquent()->create();

        // Three overdue invoices (still delinquent, already flagged)
        $this->makeOverdueDuesInvoice($property, '2026-01');
        $this->makeOverdueDuesInvoice($property, '2026-02');
        $this->makeOverdueDuesInvoice($property, '2026-03');

        $service = app(DelinquencyServiceInterface::class);
        $result  = $service->runEscalation();

        // Was already delinquent — should not be counted as newly flagged
        $this->assertSame(0, $result['flagged']);
        $this->assertSame(0, $result['cleared']);
    }

    // ── Artisan command ───────────────────────────────────────────────────────

    public function test_artisan_command_runs_delinquency_check(): void
    {
        $property = Property::factory()->create(['is_delinquent' => false]);

        $this->makeOverdueDuesInvoice($property, '2026-01');
        $this->makeOverdueDuesInvoice($property, '2026-02');
        $this->makeOverdueDuesInvoice($property, '2026-03');

        $this->artisan('hoa:check-delinquency')
             ->assertExitCode(0)
             ->expectsOutputToContain('Flagged: 1');
    }

    public function test_artisan_command_reports_zero_when_no_delinquency(): void
    {
        Property::factory()->create(['is_delinquent' => false]);

        $this->artisan('hoa:check-delinquency')
             ->assertExitCode(0)
             ->expectsOutputToContain('Flagged: 0');
    }

    // ── isDelinquent helper ───────────────────────────────────────────────────

    public function test_is_delinquent_returns_true_when_threshold_met(): void
    {
        $property = Property::factory()->create();

        $this->makeOverdueDuesInvoice($property, '2026-01');
        $this->makeOverdueDuesInvoice($property, '2026-02');
        $this->makeOverdueDuesInvoice($property, '2026-03');

        $service = app(DelinquencyServiceInterface::class);

        $this->assertTrue($service->isDelinquent($property));
    }

    public function test_is_delinquent_returns_false_below_threshold(): void
    {
        $property = Property::factory()->create();

        $this->makeOverdueDuesInvoice($property, '2026-03');

        $service = app(DelinquencyServiceInterface::class);

        $this->assertFalse($service->isDelinquent($property));
    }
}
