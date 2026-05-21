<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ViolationStatus;
use App\Models\Property;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Seeder;

/**
 * Seeds 10 realistic HOA violation records for Springdale Village.
 *
 * Evidence images contain simulated GPS coordinates around the
 * actual coordinates of Biñan City, Laguna (14.3414° N, 121.1097° E).
 * Each image entry includes: path, lat, lng, captured_at.
 *
 * Violations cover real infraction types commonly enforced by Philippine
 * subdivision HOAs under RA 9904 (Magna Carta for Homeowners).
 *
 * Status breakdown:
 *   Draft (2) → Logged but not yet officially served
 *   Issued (3) → Official notice sent to homeowner
 *   Appealed (1) → Homeowner contested the notice
 *   Paid (2) → Fine paid; awaiting board resolution
 *   Resolved (2) → Closed; homeowner complied
 */
class ViolationSeeder extends Seeder
{
    public function run(): void
    {
        $reporter  = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $treasurer = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();

        $violations = $this->violations($reporter->id);

        foreach ($violations as $data) {
            Violation::create($data);
        }

        $counts = Violation::selectRaw('status, COUNT(*) as total')
                           ->groupBy('status')
                           ->pluck('total', 'status')
                           ->toArray();

        $summary = implode(', ', array_map(
            fn ($s, $c) => "{$s}: {$c}",
            array_keys($counts),
            $counts,
        ));

        $this->command->info("  ✔ Seeded " . Violation::count() . " violations ({$summary}).");
    }

    /** @return array<int, array<string, mixed>> */
    private function violations(int $reporterId): array
    {
        return [
            // ──────────────────────────────────────────────────────────────────
            // 1. DRAFT – B1-L01 │ Excessive noise during rest hours
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B1-L01')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Excessive Noise During Rest Hours',
                'description'     => 'Resident was reported to have operated a high-powered sound system '
                    . 'at 11:45 PM on May 15, 2026, disturbing multiple neighboring households. '
                    . 'Three (3) separate noise complaints were received by the HOA office within one hour. '
                    . 'Violation of Springdale Village House Rules Section 4.2 (Quiet Hours: 10:00 PM – 6:00 AM).',
                'status'          => ViolationStatus::Draft,
                'fine_amount'     => 0.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/05/b1-l01-noise-001.jpg',
                        'lat'         => 14.34187,
                        'lng'         => 121.10923,
                        'captured_at' => '2026-05-15 23:47:00',
                    ],
                ],
                'issued_at'       => null,
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 2. DRAFT – B4-L01 │ Unkempt lawn and overgrown vegetation
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B4-L01')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Unkempt Lawn and Overgrown Vegetation',
                'description'     => 'Front lawn of the unit has been left unmaintained for an estimated 6 weeks. '
                    . 'Grass and weeds have grown beyond the 8-inch maximum height prescribed in the HOA deed of '
                    . 'restrictions. The overgrowth now encroaches onto the sidewalk easement and has been identified '
                    . 'as a potential mosquito breeding ground by the Biñan City BPHO inspection on May 10, 2026.',
                'status'          => ViolationStatus::Draft,
                'fine_amount'     => 0.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/05/b4-l01-lawn-001.jpg',
                        'lat'         => 14.33951,
                        'lng'         => 121.10744,
                        'captured_at' => '2026-05-17 09:15:00',
                    ],
                    [
                        'path'        => 'violations/2026/05/b4-l01-lawn-002.jpg',
                        'lat'         => 14.33948,
                        'lng'         => 121.10748,
                        'captured_at' => '2026-05-17 09:17:00',
                    ],
                ],
                'issued_at'       => null,
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 3. ISSUED – B1-L02 │ Improper garbage disposal
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B1-L02')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Improper Garbage Disposal (Non-Segregation)',
                'description'     => 'Resident placed unsegregated household waste outside their gate at 5:30 AM '
                    . 'on a non-collection day (May 12, 2026, Tuesday). Collection schedule per MENRO-Biñan is '
                    . 'Mon/Wed/Fri. Mixed biodegradable and non-biodegradable waste was left on the curb, '
                    . 'attracting stray cats and creating a sanitation hazard. '
                    . 'This is the second offense within 60 days (first offense: March 28, 2026).',
                'status'          => ViolationStatus::Issued,
                'fine_amount'     => 500.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/05/b1-l02-garbage-001.jpg',
                        'lat'         => 14.34212,
                        'lng'         => 121.10901,
                        'captured_at' => '2026-05-12 05:35:00',
                    ],
                    [
                        'path'        => 'violations/2026/05/b1-l02-garbage-002.jpg',
                        'lat'         => 14.34209,
                        'lng'         => 121.10904,
                        'captured_at' => '2026-05-12 05:36:00',
                    ],
                ],
                'issued_at'       => '2026-05-14 10:00:00',
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 4. ISSUED – B3-L01 │ Illegal parking blocking neighbor's driveway
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B3-L01')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Illegal Parking – Blocking Adjacent Driveway',
                'description'     => 'A Toyota Innova with plate number ABC-1234 registered under the occupant of '
                    . 'B3-L01 was parked blocking the shared driveway access of B3-L02 for approximately 4 hours '
                    . 'on May 8, 2026 (8:00 AM – 12:00 PM). The occupant of B3-L02 was unable to take their vehicle '
                    . 'out for a medical appointment and filed a formal complaint with the HOA office. '
                    . 'Per Subdivision Rules Section 6.1, no vehicle may obstruct the driveway of another lot.',
                'status'          => ViolationStatus::Issued,
                'fine_amount'     => 750.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/05/b3-l01-parking-001.jpg',
                        'lat'         => 14.34089,
                        'lng'         => 121.10812,
                        'captured_at' => '2026-05-08 08:05:00',
                    ],
                    [
                        'path'        => 'violations/2026/05/b3-l01-parking-002.jpg',
                        'lat'         => 14.34085,
                        'lng'         => 121.10809,
                        'captured_at' => '2026-05-08 11:52:00',
                    ],
                ],
                'issued_at'       => '2026-05-09 14:30:00',
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 5. ISSUED – B4-L03 │ Unauthorized commercial activity (sari-sari store)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B4-L03')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Unauthorized Commercial Activity – Sari-Sari Store Operation',
                'description'     => 'Resident has been operating a sari-sari store out of their garage since '
                    . 'approximately February 2026 without obtaining prior written approval from the HOA Board '
                    . 'as required by the Deed of Restrictions Article III, Section 3 (Residential Use Only). '
                    . 'The store is open from 6:00 AM – 10:00 PM, causing increased foot traffic, noise from '
                    . 'customers, and vehicles parked along the road in front of the property. Biñan City '
                    . 'Business Permit verification confirms no commercial permit was issued for this address.',
                'status'          => ViolationStatus::Issued,
                'fine_amount'     => 3000.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/04/b4-l03-commercial-001.jpg',
                        'lat'         => 14.33978,
                        'lng'         => 121.10766,
                        'captured_at' => '2026-04-25 10:15:00',
                    ],
                    [
                        'path'        => 'violations/2026/04/b4-l03-commercial-002.jpg',
                        'lat'         => 14.33975,
                        'lng'         => 121.10770,
                        'captured_at' => '2026-04-25 10:17:00',
                    ],
                    [
                        'path'        => 'violations/2026/04/b4-l03-commercial-003.jpg',
                        'lat'         => 14.33972,
                        'lng'         => 121.10772,
                        'captured_at' => '2026-04-25 10:19:00',
                    ],
                ],
                'issued_at'       => '2026-04-28 09:00:00',
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 6. APPEALED – B1-L04 │ Unauthorized structure modification (grille extension)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B1-L04')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Unauthorized Structure Modification – Front Grille Extension',
                'description'     => 'Resident extended the front fence grilles by approximately 60 cm beyond '
                    . 'the property boundary line, encroaching on the 1-meter HOA-owned sidewalk easement. '
                    . 'Work was performed without submitting a Renovation Permit Application to the HOA '
                    . 'Architectural Committee as required under the Deed of Restrictions Article V. '
                    . 'No Barangay building clearance was obtained. The extension was completed around April 5, 2026. '
                    . 'Resident subsequently filed an appeal on May 3, 2026 citing necessity due to vehicle security concerns.',
                'status'          => ViolationStatus::Appealed,
                'fine_amount'     => 2000.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/04/b1-l04-structure-001.jpg',
                        'lat'         => 14.34198,
                        'lng'         => 121.10917,
                        'captured_at' => '2026-04-10 14:30:00',
                    ],
                    [
                        'path'        => 'violations/2026/04/b1-l04-structure-002.jpg',
                        'lat'         => 14.34201,
                        'lng'         => 121.10914,
                        'captured_at' => '2026-04-10 14:32:00',
                    ],
                ],
                'issued_at'       => '2026-04-15 10:00:00',
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 7. PAID – B2-L03 │ Noise disturbance (karaoke past 10 PM)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B2-L03')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Noise Disturbance – Karaoke Machine Past 10:00 PM',
                'description'     => 'Resident operated a karaoke machine from 9:00 PM to 1:00 AM on '
                    . 'March 22, 2026 (Saturday), despite verbal warnings at 10:15 PM and 11:30 PM from the '
                    . 'Bayguard security guard on duty. Four (4) complaints were received from neighboring units '
                    . 'B2-L02, B2-L04, B3-L01, and B3-L02. Violation of House Rules Section 4.2 (Quiet Hours). '
                    . 'Fine was settled in full on April 2, 2026 with an official written apology submitted to the board.',
                'status'          => ViolationStatus::Paid,
                'fine_amount'     => 1000.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/03/b2-l03-noise-001.jpg',
                        'lat'         => 14.34055,
                        'lng'         => 121.10855,
                        'captured_at' => '2026-03-22 22:18:00',
                    ],
                ],
                'issued_at'       => '2026-03-25 09:00:00',
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 8. PAID – B5-L01 │ Unregistered vehicle parked on common area
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B5-L01')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Unregistered Vehicle Parked on HOA Common Area',
                'description'     => 'A motorcycle without a license plate (believed to be a second-hand unit '
                    . 'awaiting OR/CR transfer) was parked on the HOA common parking bay adjacent to Block 5 '
                    . 'for 12 consecutive days (February 15–27, 2026). The motorcycle was confirmed owned by '
                    . 'the occupant of B5-L01 after a barangay tanod verification. Per HOA Vehicle Policy '
                    . 'Section 7.3, all vehicles must be LTO-registered before being parked within the subdivision. '
                    . 'Fine was paid on March 10, 2026 and the vehicle was transferred to a private storage facility.',
                'status'          => ViolationStatus::Paid,
                'fine_amount'     => 500.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/02/b5-l01-vehicle-001.jpg',
                        'lat'         => 14.33821,
                        'lng'         => 121.10638,
                        'captured_at' => '2026-02-18 07:45:00',
                    ],
                    [
                        'path'        => 'violations/2026/02/b5-l01-vehicle-002.jpg',
                        'lat'         => 14.33818,
                        'lng'         => 121.10641,
                        'captured_at' => '2026-02-22 08:10:00',
                    ],
                ],
                'issued_at'       => '2026-02-20 11:00:00',
                'resolved_at'     => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 9. RESOLVED – B2-L01 │ Pet running loose without a leash
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B2-L01')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Pet Running Loose Without Leash (Aspin, Brown, Male)',
                'description'     => 'An unleashed medium-sized aspin (asong Pilipino) dog belonging to '
                    . 'the occupant of B2-L01 was found roaming the Ilang-ilang Street vicinity at approximately '
                    . '6:15 AM on January 14, 2026. The dog charged at a jogging resident from B2-L04, who '
                    . 'sustained a minor leg abrasion. Incident report filed with Barangay San Antonio. '
                    . 'Violation of HOA Pet Policy Section 9.1 (all pets must be leashed outside the home). '
                    . 'Resident complied: dog was vaccinated, leash training completed, and HOA received a '
                    . 'clearance letter from a licensed veterinarian on January 30, 2026.',
                'status'          => ViolationStatus::Resolved,
                'fine_amount'     => 500.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2026/01/b2-l01-pet-001.jpg',
                        'lat'         => 14.34063,
                        'lng'         => 121.10842,
                        'captured_at' => '2026-01-14 06:17:00',
                    ],
                ],
                'issued_at'       => '2026-01-16 10:00:00',
                'resolved_at'     => '2026-01-31 15:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 10. RESOLVED – B3-L02 │ Burning of garbage within the subdivision
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'     => Property::where('unit_number', 'B3-L02')->value('id'),
                'reported_by'     => $reporterId,
                'title'           => 'Open Burning of Household Waste Within Subdivision',
                'description'     => 'Occupant of B3-L02 was observed burning a pile of dried leaves and '
                    . 'mixed household waste (plastic wrappers included) in their backyard at approximately '
                    . '4:00 PM on December 18, 2025. Open burning is explicitly prohibited under RA 8749 '
                    . '(Philippine Clean Air Act) and Biñan City Ordinance No. 2019-021. '
                    . 'Smoke from the fire affected neighboring lots B3-L01 and B3-L03. '
                    . 'The Biñan City Environment & Natural Resources Office (CENRO) was notified. '
                    . 'Resident settled the fine in full on January 5, 2026, attended an environmental '
                    . 'awareness seminar hosted by the HOA, and the violation was resolved.',
                'status'          => ViolationStatus::Resolved,
                'fine_amount'     => 1500.00,
                'evidence_images' => [
                    [
                        'path'        => 'violations/2025/12/b3-l02-burning-001.jpg',
                        'lat'         => 14.34102,
                        'lng'         => 121.10823,
                        'captured_at' => '2025-12-18 16:05:00',
                    ],
                    [
                        'path'        => 'violations/2025/12/b3-l02-burning-002.jpg',
                        'lat'         => 14.34099,
                        'lng'         => 121.10826,
                        'captured_at' => '2025-12-18 16:08:00',
                    ],
                    [
                        'path'        => 'violations/2025/12/b3-l02-burning-003.jpg',
                        'lat'         => 14.34096,
                        'lng'         => 121.10820,
                        'captured_at' => '2025-12-18 16:11:00',
                    ],
                ],
                'issued_at'       => '2025-12-22 09:00:00',
                'resolved_at'     => '2026-01-07 10:00:00',
            ],
        ];
    }
}
