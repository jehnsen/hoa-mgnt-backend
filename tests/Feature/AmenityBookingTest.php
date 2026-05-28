<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceType;
use App\Models\Amenity;
use App\Models\AmenityBlackout;
use App\Models\AmenityBooking;
use App\Models\Property;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AmenityBookingTest extends TestCase
{
    use RefreshDatabase;

    private function bookingPayload(Amenity $amenity, Property $property, array $overrides = []): array
    {
        return array_merge([
            'amenity_id'  => $amenity->uuid,
            'property_id' => $property->uuid,
            'title'       => 'Pool party',
            'start_at'    => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
            'end_at'      => Carbon::tomorrow()->setTime(12, 0)->toDateTimeString(),
            'notes'       => null,
        ], $overrides);
    }

    // ── Happy path ────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_book_an_amenity(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property));

        $response->assertCreated()
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status', 'pending');
    }

    public function test_booking_confirmation_notification_sent_to_booker(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property))
            ->assertCreated();

        Notification::assertSentTo($user, BookingConfirmedNotification::class);
    }

    // ── Inactive amenity ──────────────────────────────────────────────────────

    public function test_booking_inactive_amenity_returns_422(): void
    {
        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->inactive()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property))
            ->assertStatus(422);
    }

    // ── Conflict detection ────────────────────────────────────────────────────

    public function test_overlapping_booking_returns_409(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create();

        $start = Carbon::tomorrow()->setTime(10, 0);
        $end   = Carbon::tomorrow()->setTime(12, 0);

        // First booking succeeds
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => $start->toDateTimeString(),
                'end_at'   => $end->toDateTimeString(),
            ]))
            ->assertCreated();

        // Second overlapping booking conflicts
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => $start->copy()->addHour()->toDateTimeString(),
                'end_at'   => $end->copy()->addHours(2)->toDateTimeString(),
            ]))
            ->assertStatus(409);
    }

    public function test_non_overlapping_bookings_are_allowed(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create();

        // First booking: 10:00–12:00
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
                'end_at'   => Carbon::tomorrow()->setTime(12, 0)->toDateTimeString(),
            ]))
            ->assertCreated();

        // Second booking: 14:00–16:00 (no overlap)
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => Carbon::tomorrow()->setTime(14, 0)->toDateTimeString(),
                'end_at'   => Carbon::tomorrow()->setTime(16, 0)->toDateTimeString(),
            ]))
            ->assertCreated();
    }

    // ── Blackout periods ──────────────────────────────────────────────────────

    public function test_booking_during_blackout_returns_409(): void
    {
        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create();

        AmenityBlackout::create([
            'amenity_id' => $amenity->id,
            'start_at'   => Carbon::tomorrow()->setTime(9, 0),
            'end_at'     => Carbon::tomorrow()->setTime(13, 0),
            'reason'     => 'Maintenance',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
                'end_at'   => Carbon::tomorrow()->setTime(12, 0)->toDateTimeString(),
            ]))
            ->assertStatus(409);
    }

    // ── Monthly limit ─────────────────────────────────────────────────────────

    public function test_booking_past_monthly_limit_returns_422(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->withMonthlyLimit(1)->create();

        // First booking fills the limit
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
                'end_at'   => Carbon::tomorrow()->setTime(12, 0)->toDateTimeString(),
            ]))
            ->assertCreated();

        // Second booking in same month is rejected
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => Carbon::tomorrow()->addDay()->setTime(14, 0)->toDateTimeString(),
                'end_at'   => Carbon::tomorrow()->addDay()->setTime(16, 0)->toDateTimeString(),
            ]))
            ->assertStatus(422);
    }

    // ── Fee invoice ───────────────────────────────────────────────────────────

    public function test_booking_fee_amenity_creates_invoice(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->withFee(500.00, 0.00)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property, [
                'start_at' => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
                'end_at'   => Carbon::tomorrow()->setTime(12, 0)->toDateTimeString(),
            ]));

        $response->assertCreated();

        $this->assertDatabaseHas('invoices', [
            'property_id' => $property->id,
            'type'        => InvoiceType::AmenityBookingFee->value,
        ]);
    }

    public function test_free_amenity_booking_creates_no_invoice(): void
    {
        Notification::fake();

        $user     = User::factory()->resident()->create();
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create(); // no fee

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property))
            ->assertCreated();

        $this->assertDatabaseMissing('invoices', [
            'property_id' => $property->id,
            'type'        => InvoiceType::AmenityBookingFee->value,
        ]);
    }

    // ── Auth guard ────────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_book(): void
    {
        $property = Property::factory()->create();
        $amenity  = Amenity::factory()->create();

        $this->postJson('/api/v1/bookings', $this->bookingPayload($amenity, $property))
             ->assertUnauthorized();
    }
}
