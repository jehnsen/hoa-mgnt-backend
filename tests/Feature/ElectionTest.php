<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ElectionStatus;
use App\Enums\NominationStatus;
use App\Models\BoardElection;
use App\Models\ElectionNomination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ElectionTest extends TestCase
{
    use RefreshDatabase;

    private function electionPayload(array $overrides = []): array
    {
        return array_merge([
            'title'               => 'Board of Directors Election 2026',
            'description'         => 'Annual election for board positions.',
            'nomination_deadline' => Carbon::tomorrow()->toDateString(),
            'voting_open_at'      => Carbon::tomorrow()->addDays(2)->toIso8601String(),
            'voting_close_at'     => Carbon::tomorrow()->addDays(5)->toIso8601String(),
        ], $overrides);
    }

    // ── Creation ──────────────────────────────────────────────────────────────

    public function test_super_admin_can_create_election(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/elections', $this->electionPayload());

        $response->assertCreated()
                 ->assertJsonPath('data.status', ElectionStatus::Draft->value);
    }

    public function test_board_member_can_create_election(): void
    {
        $board = User::factory()->boardMember()->create();

        $this->actingAs($board, 'sanctum')
            ->postJson('/api/v1/elections', $this->electionPayload())
            ->assertCreated();
    }

    public function test_resident_cannot_create_election(): void
    {
        $resident = User::factory()->resident()->create();

        $this->actingAs($resident, 'sanctum')
            ->postJson('/api/v1/elections', $this->electionPayload())
            ->assertForbidden();
    }

    // ── Status machine — valid transitions ────────────────────────────────────

    public function test_draft_can_transition_to_nominations_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $election = BoardElection::factory()->create(['status' => ElectionStatus::Draft, 'created_by' => $admin->id]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/elections/{$election->uuid}/status", [
                'status' => ElectionStatus::NominationsOpen->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', ElectionStatus::NominationsOpen->value);
    }

    public function test_nominations_open_can_transition_to_voting_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::NominationsOpen,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/elections/{$election->uuid}/status", [
                'status' => ElectionStatus::VotingOpen->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', ElectionStatus::VotingOpen->value);
    }

    public function test_voting_open_can_transition_to_closed(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::VotingOpen,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/elections/{$election->uuid}/status", [
                'status' => ElectionStatus::Closed->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', ElectionStatus::Closed->value);
    }

    public function test_closed_can_transition_to_certified(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::Closed,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/elections/{$election->uuid}/status", [
                'status' => ElectionStatus::Certified->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', ElectionStatus::Certified->value);
    }

    // ── Status machine — invalid transitions ──────────────────────────────────

    public function test_draft_cannot_jump_to_voting_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::Draft,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/elections/{$election->uuid}/status", [
                'status' => ElectionStatus::VotingOpen->value,
            ])
            ->assertStatus(422);
    }

    public function test_certified_election_cannot_be_transitioned(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::Certified,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/v1/elections/{$election->uuid}/status", [
                'status' => ElectionStatus::Draft->value,
            ])
            ->assertStatus(422);
    }

    // ── Nominations ───────────────────────────────────────────────────────────

    public function test_nomination_can_be_submitted_during_nominations_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::NominationsOpen,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/nominations", [
                'nominee_id'          => $nominee->uuid,
                'position_value'      => 'president',
                'candidate_statement' => 'I will serve the community faithfully.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', NominationStatus::Pending->value);
    }

    public function test_nomination_rejected_when_nominations_not_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::Draft,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/nominations", [
                'nominee_id'     => $nominee->uuid,
                'position_value' => 'president',
            ])
            ->assertStatus(422);
    }

    public function test_duplicate_nomination_for_same_position_returns_409(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::NominationsOpen,
            'created_by' => $admin->id,
        ]);

        $payload = [
            'nominee_id'     => $nominee->uuid,
            'position_value' => 'treasurer',
        ];

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/nominations", $payload)
            ->assertCreated();

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/nominations", $payload)
            ->assertStatus(409);
    }

    // ── Voting ────────────────────────────────────────────────────────────────

    public function test_vote_can_be_cast_during_voting_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $voter    = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::VotingOpen,
            'created_by' => $admin->id,
        ]);

        $nomination = ElectionNomination::create([
            'election_id'    => $election->id,
            'nominee_id'     => $nominee->id,
            'nominated_by'   => $admin->id,
            'position_value' => 'president',
            'status'         => NominationStatus::Accepted->value,
        ]);

        $this->actingAs($voter, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/vote", [
                'nomination_id' => $nomination->uuid,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_cannot_vote_when_voting_not_open(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $voter    = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::NominationsOpen,
            'created_by' => $admin->id,
        ]);

        $nomination = ElectionNomination::create([
            'election_id'    => $election->id,
            'nominee_id'     => $nominee->id,
            'nominated_by'   => $admin->id,
            'position_value' => 'president',
            'status'         => NominationStatus::Accepted->value,
        ]);

        $this->actingAs($voter, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/vote", [
                'nomination_id' => $nomination->uuid,
            ])
            ->assertStatus(422);
    }

    public function test_duplicate_vote_for_same_position_returns_409(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $voter    = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::VotingOpen,
            'created_by' => $admin->id,
        ]);

        $nomination = ElectionNomination::create([
            'election_id'    => $election->id,
            'nominee_id'     => $nominee->id,
            'nominated_by'   => $admin->id,
            'position_value' => 'president',
            'status'         => NominationStatus::Accepted->value,
        ]);

        $this->actingAs($voter, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/vote", [
                'nomination_id' => $nomination->uuid,
            ])
            ->assertOk();

        // Second vote for same position
        $this->actingAs($voter, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/vote", [
                'nomination_id' => $nomination->uuid,
            ])
            ->assertStatus(409);
    }

    public function test_cannot_vote_for_unaccepted_nomination(): void
    {
        $admin    = User::factory()->superAdmin()->create();
        $nominee  = User::factory()->resident()->create();
        $voter    = User::factory()->resident()->create();
        $election = BoardElection::factory()->create([
            'status'     => ElectionStatus::VotingOpen,
            'created_by' => $admin->id,
        ]);

        $nomination = ElectionNomination::create([
            'election_id'    => $election->id,
            'nominee_id'     => $nominee->id,
            'nominated_by'   => $admin->id,
            'position_value' => 'president',
            'status'         => NominationStatus::Pending->value,
        ]);

        $this->actingAs($voter, 'sanctum')
            ->postJson("/api/v1/elections/{$election->uuid}/vote", [
                'nomination_id' => $nomination->uuid,
            ])
            ->assertStatus(422);
    }
}
