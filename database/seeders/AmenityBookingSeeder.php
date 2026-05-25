<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 10 realistic facility booking records for Springdale Village.
 *
 * Covers the Multipurpose Hall, Basketball Court, and Swimming Pool.
 * The Function Room is excluded as it is marked inactive (under renovation).
 *
 * Status breakdown:
 *   Pending (3)   – Awaiting board confirmation
 *   Confirmed (5) – Board-approved reservations
 *   Cancelled (2) – Cancelled by the booker or the HOA
 */
class AmenityBookingSeeder extends Seeder
{
    public function run(): void
    {
        $hall  = Amenity::where('name', 'Multipurpose Hall')->firstOrFail();
        $court = Amenity::where('name', 'Basketball Court')->firstOrFail();
        $pool  = Amenity::where('name', 'Swimming Pool')->firstOrFail();

        $residents = [
            'b1l01' => User::where('email', 'carlos.reyes@gmail.com')->firstOrFail(),
            'b1l03' => User::where('email', 'eduardo.lopez@yahoo.com')->firstOrFail(),
            'b2l02' => User::where('email', 'antonio.hernandez@gmail.com')->firstOrFail(),
            'b2l04' => User::where('email', 'roberto.castillo@gmail.com')->firstOrFail(),
            'b3l01' => User::where('email', 'diana.navarro@gmail.com')->firstOrFail(),
            'b3l02' => User::where('email', 'emmanuel.soriano@gmail.com')->firstOrFail(),
            'b4l01' => User::where('email', 'felicidad.espiritu@gmail.com')->firstOrFail(),
            'b5l01' => User::where('email', 'benjamin.magno@gmail.com')->firstOrFail(),
        ];

        $props = [
            'b1l01' => Property::where('unit_number', 'B1-L01')->value('id'),
            'b1l03' => Property::where('unit_number', 'B1-L03')->value('id'),
            'b2l02' => Property::where('unit_number', 'B2-L02')->value('id'),
            'b2l04' => Property::where('unit_number', 'B2-L04')->value('id'),
            'b3l01' => Property::where('unit_number', 'B3-L01')->value('id'),
            'b3l02' => Property::where('unit_number', 'B3-L02')->value('id'),
            'b4l01' => Property::where('unit_number', 'B4-L01')->value('id'),
            'b5l01' => Property::where('unit_number', 'B5-L01')->value('id'),
        ];

        $bookings = $this->bookings($hall, $court, $pool, $residents, $props);

        foreach ($bookings as $data) {
            AmenityBooking::create($data);
        }

        $counts = AmenityBooking::selectRaw('status, COUNT(*) as total')
                                ->groupBy('status')
                                ->pluck('total', 'status')
                                ->toArray();

        $summary = implode(', ', array_map(fn ($s, $c) => "{$s}: {$c}", array_keys($counts), $counts));

        $this->command->info("  ✔ Seeded " . AmenityBooking::count() . " amenity bookings ({$summary}).");
    }

    /** @return array<int, array<string, mixed>> */
    private function bookings(
        Amenity $hall,
        Amenity $court,
        Amenity $pool,
        array   $residents,
        array   $props,
    ): array {
        return [
            // ──────────────────────────────────────────────────────────────────
            // 1. CONFIRMED – Hall | Annual General Assembly 2026
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $hall->id,
                'property_id'         => $props['b1l01'],
                'booked_by'           => $residents['b1l01']->id,
                'title'               => 'Annual General Assembly 2026',
                'start_at'            => '2026-06-14 14:00:00',
                'end_at'              => '2026-06-14 18:00:00',
                'status'              => BookingStatus::Confirmed,
                'notes'               => 'Official HOA event. Board-coordinated. Setup begins at 12:00 PM. '
                    . 'Caterer (Lola Nena\'s Catering) to arrive at 12:30 PM. AV team at 1:00 PM.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 2. CONFIRMED – Hall | Reyes Family Birthday Party
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $hall->id,
                'property_id'         => $props['b1l01'],
                'booked_by'           => $residents['b1l01']->id,
                'title'               => 'Birthday Celebration – Carmela Reyes (50th)',
                'start_at'            => '2026-06-06 16:00:00',
                'end_at'              => '2026-06-06 22:00:00',
                'status'              => BookingStatus::Confirmed,
                'notes'               => 'Private family event. Approximately 60 guests. '
                    . 'External caterer approved (Kusina ni Nanay). Sound system from outside allowed.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 3. CONFIRMED – Court | Block 3 vs Block 4 Basketball Tournament
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $court->id,
                'property_id'         => $props['b3l01'],
                'booked_by'           => $residents['b3l01']->id,
                'title'               => 'Inter-Block Basketball Tournament (Block 3 vs Block 4)',
                'start_at'            => '2026-05-31 08:00:00',
                'end_at'              => '2026-05-31 12:00:00',
                'status'              => BookingStatus::Confirmed,
                'notes'               => 'Best-of-3 series. Referee and scorekeeper confirmed from Block 5. '
                    . 'Snacks and water provided by each block. No chairs may be placed inside the court boundary.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 4. CONFIRMED – Court | Block 1 & 2 Kids Basketball Training
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $court->id,
                'property_id'         => $props['b1l03'],
                'booked_by'           => $residents['b1l03']->id,
                'title'               => 'Youth Basketball Training Session – Blocks 1 & 2',
                'start_at'            => '2026-06-07 07:00:00',
                'end_at'              => '2026-06-07 09:30:00',
                'status'              => BookingStatus::Confirmed,
                'notes'               => 'Organized by Mr. Eduardo Lopez. 12 children ages 8–14. '
                    . 'Adult supervising coach will be present at all times.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 5. CONFIRMED – Pool | Swimming Lessons for Kids (Block 4)
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $pool->id,
                'property_id'         => $props['b4l01'],
                'booked_by'           => $residents['b4l01']->id,
                'title'               => 'Private Swimming Lessons – Espiritu Children',
                'start_at'            => '2026-06-01 07:00:00',
                'end_at'              => '2026-06-01 09:00:00',
                'status'              => BookingStatus::Confirmed,
                'notes'               => 'Certified swim instructor (external guest, Ms. Anabelle Cruz) to accompany. '
                    . 'Guest entry request filed separately with security.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 6. PENDING – Hall | HOA Livelihood Seminar (Board-organized)
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $hall->id,
                'property_id'         => $props['b2l02'],
                'booked_by'           => $residents['b2l02']->id,
                'title'               => 'Livelihood Seminar – DTI Negosyo Center x Springdale Village HOA',
                'start_at'            => '2026-06-20 09:00:00',
                'end_at'              => '2026-06-20 12:00:00',
                'status'              => BookingStatus::Pending,
                'notes'               => 'Partnership with DTI-Laguna Negosyo Center. Free seminar for all residents. '
                    . 'Expecting 40–50 attendees. Awaiting board confirmation and DTI speaker confirmation.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 7. PENDING – Court | Sunday Pickleball Session
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $court->id,
                'property_id'         => $props['b2l04'],
                'booked_by'           => $residents['b2l04']->id,
                'title'               => 'Sunday Pickleball – Blocks 2 & 5 Residents',
                'start_at'            => '2026-06-08 07:00:00',
                'end_at'              => '2026-06-08 10:00:00',
                'status'              => BookingStatus::Pending,
                'notes'               => 'Group of 8 players (4 from Block 2, 4 from Block 5). '
                    . 'Will bring portable pickleball net. Request to use the east half of the court.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 8. PENDING – Pool | Block 5 Kids Pool Party
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $pool->id,
                'property_id'         => $props['b5l01'],
                'booked_by'           => $residents['b5l01']->id,
                'title'               => 'End-of-School-Year Pool Party – Block 5 Kids',
                'start_at'            => '2026-06-13 14:00:00',
                'end_at'              => '2026-06-13 17:00:00',
                'status'              => BookingStatus::Pending,
                'notes'               => 'Approximately 10 children (6–14 years old) with parents present. '
                    . 'Request exclusive pool use for the 3-hour block. Food to be served at the poolside table only.',
                'cancelled_at'        => null,
                'cancellation_reason' => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 9. CANCELLED – Hall | Cancelled birthday event (personal reason)
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $hall->id,
                'property_id'         => $props['b3l02'],
                'booked_by'           => $residents['b3l02']->id,
                'title'               => 'Soriano Family Reunion – May 2026',
                'start_at'            => '2026-05-25 15:00:00',
                'end_at'              => '2026-05-25 21:00:00',
                'status'              => BookingStatus::Cancelled,
                'notes'               => 'Originally planned for 50 guests. External catering arranged.',
                'cancelled_at'        => '2026-05-18 10:30:00',
                'cancellation_reason' => 'Family member had a medical emergency. Event postponed to a later date. '
                    . 'Cancellation was made more than 5 business days before the event; no cancellation fee applies.',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 10. CANCELLED – Court | Cancelled due to conflicting HOA event
            // ──────────────────────────────────────────────────────────────────
            [
                'amenity_id'          => $court->id,
                'property_id'         => $props['b2l02'],
                'booked_by'           => $residents['b2l02']->id,
                'title'               => 'Block 2 Residents Volleyball Practice',
                'start_at'            => '2026-06-14 09:00:00',
                'end_at'              => '2026-06-14 11:00:00',
                'status'              => BookingStatus::Cancelled,
                'notes'               => 'Group of 12 residents. Requested exclusive court use for volleyball.',
                'cancelled_at'        => '2026-06-03 09:00:00',
                'cancellation_reason' => 'Cancelled by HOA – court area is needed for AGA 2026 overflow parking '
                    . 'and guest overflow on June 14. Residents were notified and offered alternative dates.',
            ],
        ];
    }
}
