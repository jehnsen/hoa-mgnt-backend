<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ViolationStatus;
use App\Models\Property;
use App\Models\User;
use App\Models\Violation;
use App\Notifications\ViolationIssuedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ViolationTest extends TestCase
{
    use RefreshDatabase;

    // ── Creation ─────────────────────────────────────────────────────────────

    public function test_board_member_can_create_violation(): void
    {
        $board    = User::factory()->boardMember()->create();
        $property = Property::factory()->create();

        $response = $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/violations', [
                'property_id' => $property->uuid,
                'title'       => 'Unauthorized parking in fire lane',
                'description' => 'Vehicle parked in designated fire lane for more than 30 minutes blocking emergency access.',
                'category'    => 'parking',
                'fine_amount' => 500,
            ]);

        $response->assertCreated()
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status', 'draft');

        $this->assertDatabaseHas('violations', [
            'property_id' => $property->id,
            'status'      => 'draft',
        ]);
    }

    public function test_resident_cannot_create_violation(): void
    {
        $resident = User::factory()->resident()->create();
        $property = Property::factory()->create();

        $this->actingAs($resident, 'sanctum')
            ->postJson('/api/v1/violations', [
                'property_id' => $property->uuid,
                'title'       => 'Noise complaint about neighbor',
                'description' => 'Neighbor has been playing loud music past midnight repeatedly.',
                'category'    => 'noise',
            ])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_violation(): void
    {
        $property = Property::factory()->create();

        $this->postJson('/api/v1/violations', [
            'property_id' => $property->uuid,
            'title'       => 'Noise complaint',
            'description' => 'Repeated late-night noise disturbing neighbors and the peace.',
        ])->assertUnauthorized();
    }

    // ── State Machine — valid transitions ─────────────────────────────────────

    public function test_board_member_can_transition_draft_to_issued(): void
    {
        Notification::fake();

        $board     = User::factory()->boardMember()->create();
        $violation = Violation::factory()->draft()->create();

        $response = $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", [
                'status' => 'issued',
            ]);

        $response->assertOk()
                 ->assertJsonPath('data.status', 'issued');

        $this->assertDatabaseHas('violations', [
            'id'     => $violation->id,
            'status' => 'issued',
        ]);
    }

    public function test_issued_violation_can_be_appealed(): void
    {
        $board     = User::factory()->boardMember()->create();
        $violation = Violation::factory()->issued()->create();

        $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", ['status' => 'appealed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'appealed');
    }

    public function test_issued_violation_can_be_resolved_directly(): void
    {
        $board     = User::factory()->boardMember()->create();
        $violation = Violation::factory()->issued()->create();

        $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", ['status' => 'resolved'])
            ->assertOk()
            ->assertJsonPath('data.status', 'resolved');
    }

    // ── State Machine — invalid transitions ───────────────────────────────────

    public function test_draft_violation_cannot_jump_to_resolved(): void
    {
        $board     = User::factory()->boardMember()->create();
        $violation = Violation::factory()->draft()->create();

        $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", ['status' => 'resolved'])
            ->assertStatus(409);  // InvalidViolationTransitionException → 409
    }

    public function test_draft_violation_cannot_jump_to_paid(): void
    {
        $board     = User::factory()->boardMember()->create();
        $violation = Violation::factory()->draft()->create();

        $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", ['status' => 'paid'])
            ->assertStatus(409);  // InvalidViolationTransitionException → 409
    }

    public function test_resolved_violation_cannot_be_transitioned_further(): void
    {
        $board     = User::factory()->boardMember()->create();
        $violation = Violation::factory()->create(['status' => ViolationStatus::Resolved->value]);

        $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", ['status' => 'issued'])
            ->assertStatus(409);  // InvalidViolationTransitionException → 409
    }

    // ── Notification ──────────────────────────────────────────────────────────

    public function test_ViolationIssuedNotification_sent_when_violation_issued(): void
    {
        Notification::fake();

        $board    = User::factory()->boardMember()->create();
        $resident = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $property->residents()->attach($resident, ['move_in_at' => now(), 'is_primary_resident' => true, 'is_owner' => false]);

        $violation = Violation::factory()->draft()->create(['property_id' => $property->id]);

        $this->actingAs($board, 'sanctum')
            ->patchJson("/api/v1/violations/{$violation->uuid}/status", ['status' => 'issued'])
            ->assertOk();

        Notification::assertSentTo($resident, ViolationIssuedNotification::class);
    }

    // ── Show / 404 ────────────────────────────────────────────────────────────

    public function test_show_returns_404_for_unknown_uuid(): void
    {
        $board = User::factory()->boardMember()->create();

        $this->actingAs($board, 'sanctum')
            ->getJson('/api/v1/violations/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    }

    public function test_board_member_can_list_violations(): void
    {
        $board = User::factory()->boardMember()->create();
        Violation::factory()->count(3)->create();

        $this->actingAs($board, 'sanctum')
            ->getJson('/api/v1/violations')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }
}
