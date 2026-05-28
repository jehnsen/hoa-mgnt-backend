<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    // ── Payment — full settlement ─────────────────────────────────────────────

    public function test_full_payment_marks_invoice_as_paid(): void
    {
        Notification::fake();

        $board    = User::factory()->boardMember()->create();
        $resident = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $property->residents()->attach($resident, [
            'move_in_at'          => now(),
            'is_primary_resident' => true,
            'is_owner'            => false,
        ]);
        $invoice = Invoice::factory()->create(['property_id' => $property->id]);

        $response = $this->actingAs($board, 'sanctum')
            ->postJson("/api/v1/financials/invoices/{$invoice->uuid}/payments", [
                'amount'         => 2500.00,
                'payment_method' => 'cash',
            ]);

        $response->assertCreated()
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('invoices', [
            'id'     => $invoice->id,
            'status' => InvoiceStatus::Paid->value,
        ]);
    }

    public function test_partial_payment_transitions_invoice_to_partial(): void
    {
        Notification::fake();

        $board    = User::factory()->boardMember()->create();
        $resident = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $property->residents()->attach($resident, [
            'move_in_at'          => now(),
            'is_primary_resident' => true,
            'is_owner'            => false,
        ]);
        $invoice = Invoice::factory()->create([
            'property_id' => $property->id,
            'base_amount' => 2500.00,
        ]);

        $this->actingAs($board, 'sanctum')
            ->postJson("/api/v1/financials/invoices/{$invoice->uuid}/payments", [
                'amount'         => 1000.00,
                'payment_method' => 'cash',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('invoices', [
            'id'     => $invoice->id,
            'status' => InvoiceStatus::Partial->value,
        ]);
    }

    public function test_payment_on_paid_invoice_returns_422(): void
    {
        $board    = User::factory()->boardMember()->create();
        $property = Property::factory()->create();
        $invoice  = Invoice::factory()->paid()->create(['property_id' => $property->id]);

        $this->actingAs($board, 'sanctum')
            ->postJson("/api/v1/financials/invoices/{$invoice->uuid}/payments", [
                'amount'         => 2500.00,
                'payment_method' => 'cash',
            ])
            ->assertStatus(409);
    }

    public function test_resident_cannot_process_payment(): void
    {
        $resident = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $invoice  = Invoice::factory()->create(['property_id' => $property->id]);

        $this->actingAs($resident, 'sanctum')
            ->postJson("/api/v1/financials/invoices/{$invoice->uuid}/payments", [
                'amount'         => 2500.00,
                'payment_method' => 'cash',
            ])
            ->assertForbidden();
    }

    // ── Payment notifications ─────────────────────────────────────────────────

    public function test_payment_receipt_notification_sent_to_residents(): void
    {
        Notification::fake();

        $board    = User::factory()->boardMember()->create();
        $owner    = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $property->residents()->attach($owner, [
            'move_in_at'          => now(),
            'is_primary_resident' => true,
            'is_owner'            => true,
        ]);
        $invoice = Invoice::factory()->create(['property_id' => $property->id]);

        $this->actingAs($board, 'sanctum')
            ->postJson("/api/v1/financials/invoices/{$invoice->uuid}/payments", [
                'amount'         => 2500.00,
                'payment_method' => 'bank_transfer',
            ])
            ->assertCreated();

        Notification::assertSentTo($owner, PaymentReceivedNotification::class);
    }

    // ── Late fees ─────────────────────────────────────────────────────────────

    public function test_apply_late_fees_marks_overdue_pending_invoices(): void
    {
        $board    = User::factory()->boardMember()->create();
        $property = Property::factory()->create(['late_fee_rate' => 0.05]);

        // Pending invoice with a past due date
        Invoice::factory()->create([
            'property_id'     => $property->id,
            'base_amount'     => 2500.00,
            'late_fee_amount' => 0.00,
            'status'          => InvoiceStatus::Pending->value,
            'due_at'          => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/financials/invoices/apply-late-fees');

        $response->assertOk()
                 ->assertJsonPath('data.updated_invoices', 1);

        $this->assertDatabaseHas('invoices', [
            'property_id' => $property->id,
            'status'      => InvoiceStatus::Overdue->value,
        ]);
    }

    public function test_apply_late_fees_ignores_paid_invoices(): void
    {
        $board    = User::factory()->boardMember()->create();
        $property = Property::factory()->create();

        Invoice::factory()->paid()->create([
            'property_id' => $property->id,
            'due_at'      => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/financials/invoices/apply-late-fees');

        $response->assertOk()
                 ->assertJsonPath('data.updated_invoices', 0);
    }

    // ── Invoice generation ────────────────────────────────────────────────────

    public function test_board_member_can_generate_monthly_dues_invoice(): void
    {
        $board    = User::factory()->boardMember()->create();
        $property = Property::factory()->create(['monthly_dues' => 3000.00]);

        $response = $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/financials/invoices', [
                'property_id'  => $property->uuid,
                'type'         => InvoiceType::MonthlyDues->value,
                'period_month' => '2025-06',
            ]);

        $response->assertCreated()
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('invoices', [
            'property_id' => $property->id,
            'type'        => InvoiceType::MonthlyDues->value,
            'base_amount' => '3000.00',
        ]);
    }

    public function test_duplicate_invoice_for_same_period_returns_409(): void
    {
        $board    = User::factory()->boardMember()->create();
        $property = Property::factory()->create();

        // Create first invoice via the API (ensures period_month is stored identically)
        $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/financials/invoices', [
                'property_id'  => $property->uuid,
                'type'         => InvoiceType::MonthlyDues->value,
                'period_month' => '2025-06',
            ])
            ->assertCreated();

        // Second request for the same period must be rejected
        $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/financials/invoices', [
                'property_id'  => $property->uuid,
                'type'         => InvoiceType::MonthlyDues->value,
                'period_month' => '2025-06',
            ])
            ->assertStatus(409);
    }

    // ── Resident access ───────────────────────────────────────────────────────

    public function test_resident_can_view_own_property_invoices(): void
    {
        $resident = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $property->residents()->attach($resident, [
            'move_in_at'          => now(),
            'is_primary_resident' => true,
            'is_owner'            => false,
        ]);
        Invoice::factory()->count(2)->create(['property_id' => $property->id]);

        $this->actingAs($resident, 'sanctum')
            ->getJson("/api/v1/financials/properties/{$property->uuid}/invoices")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
