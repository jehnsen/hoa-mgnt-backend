<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 9 realistic HOA maintenance request records for Springdale Village.
 *
 * Covers both common-area issues (filed by board/admin) and unit-level
 * requests (filed by residents). Reflects the typical categories handled
 * by a Philippine subdivision HOA under RA 9904.
 *
 * Status breakdown:
 *   Submitted (2)  – Newly filed, awaiting board action
 *   In Review (2)  – Being assessed / vendor inspection scheduled
 *   In Progress (2) – Repair/work actively ongoing
 *   Resolved (2)   – Work completed
 *   Closed (1)     – Closed without action (duplicate or out-of-scope)
 */
class MaintenanceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();
        $president = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $vendor    = User::where('email', 'rolando.macaraeg@springdale-hoa.ph')->firstOrFail();

        $residents = [
            'b1l01' => User::where('email', 'carlos.reyes@gmail.com')->firstOrFail(),
            'b1l02' => User::where('email', 'mariagrace.delacruz@gmail.com')->firstOrFail(),
            'b2l01' => User::where('email', 'cristina.villanueva@gmail.com')->firstOrFail(),
            'b3l01' => User::where('email', 'diana.navarro@gmail.com')->firstOrFail(),
            'b4l02' => User::where('email', 'manuel.pascual@outlook.com')->firstOrFail(),
            'b5l01' => User::where('email', 'benjamin.magno@gmail.com')->firstOrFail(),
        ];

        $requests = $this->maintenanceRequests($admin, $president, $vendor, $residents);

        foreach ($requests as $data) {
            MaintenanceRequest::create($data);
        }

        $counts = MaintenanceRequest::selectRaw('status, COUNT(*) as total')
                                    ->groupBy('status')
                                    ->pluck('total', 'status')
                                    ->toArray();

        $summary = implode(', ', array_map(fn ($s, $c) => "{$s}: {$c}", array_keys($counts), $counts));

        $this->command->info("  ✔ Seeded " . MaintenanceRequest::count() . " maintenance requests ({$summary}).");
    }

    /** @return array<int, array<string, mixed>> */
    private function maintenanceRequests(
        User  $admin,
        User  $president,
        User  $vendor,
        array $residents,
    ): array {
        return [
            // ──────────────────────────────────────────────────────────────────
            // 1. SUBMITTED – Urgent | Perimeter wall crack (Block 5)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B5-L01')->value('id'),
                'submitted_by'     => $residents['b5l01']->id,
                'assigned_to'      => null,
                'category'         => MaintenanceCategory::Structural,
                'title'            => 'Crack in Perimeter Wall – Block 5, Near Gate 2',
                'description'      => 'There is a visible horizontal crack approximately 1.5 meters long and '
                    . '3–5 cm wide running along the perimeter wall at the Block 5 side near Gate 2. '
                    . 'The crack appears to have widened significantly after the heavy rains on May 18–20, 2026. '
                    . 'I am concerned this may be a structural issue and could worsen during typhoon season. '
                    . 'Please send a structural engineer or maintenance team for an urgent assessment. '
                    . 'The wall borders my lot (B5-L01) and I would like to be informed of the findings.',
                'priority'         => MaintenancePriority::Urgent,
                'status'           => MaintenanceStatus::Submitted,
                'resolution_notes' => null,
                'resolved_at'      => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 2. SUBMITTED – Normal | Common area drainage clog (Block 3)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B3-L01')->value('id'),
                'submitted_by'     => $residents['b3l01']->id,
                'assigned_to'      => null,
                'category'         => MaintenanceCategory::Plumbing,
                'title'            => 'Clogged Street Drain – Narra Street, Block 3',
                'description'      => 'The storm drain in front of B3-L01 to B3-L03 along Narra Street has been '
                    . 'clogged with mud and debris since the heavy rains last week. Water accumulates on the road '
                    . 'surface and takes several hours to drain, creating a flooding hazard and mosquito breeding '
                    . 'ground. This has been an issue every rainy season and I am requesting a proper cleaning and '
                    . 'possible widening of the drain inlet. Please coordinate with Biñan City Engineering if '
                    . 'the issue involves the main drainage line.',
                'priority'         => MaintenancePriority::Normal,
                'status'           => MaintenanceStatus::Submitted,
                'resolution_notes' => null,
                'resolved_at'      => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 3. IN REVIEW – High | Gate motor malfunction (Main Gate)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B1-L01')->value('id'),
                'submitted_by'     => $president->id,
                'assigned_to'      => $vendor->id,
                'category'         => MaintenanceCategory::Electrical,
                'title'            => 'Main Gate Motor Malfunction – Intermittent Auto-Close Failure',
                'description'      => 'The motorized sliding gate at the main entrance (Sampaguita Street Gate) '
                    . 'has been experiencing intermittent auto-close failures since May 15, 2026. The gate opens '
                    . 'normally via remote and guard switch but fails to auto-close on approximately 30% of activations, '
                    . 'requiring manual intervention from the guard. This is a security risk as the gate remains open '
                    . 'for extended periods. The control panel display shows error code E-03 which may indicate a '
                    . 'motor encoder or limit switch issue. R.B. Macaraeg General Services has been notified and '
                    . 'will conduct an inspection on May 27, 2026 at 9:00 AM.',
                'priority'         => MaintenancePriority::High,
                'status'           => MaintenanceStatus::InReview,
                'resolution_notes' => null,
                'resolved_at'      => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 4. IN REVIEW – Normal | Clubhouse HVAC unit not cooling
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B2-L01')->value('id'),
                'submitted_by'     => $admin->id,
                'assigned_to'      => $vendor->id,
                'category'         => MaintenanceCategory::Hvac,
                'title'            => 'Clubhouse Multipurpose Hall – AC Unit 2 Not Cooling',
                'description'      => 'The second ceiling-type air conditioning unit in the Multipurpose Hall '
                    . '(2.5 HP Panasonic ceiling cassette, SN: PAN-CCR-0048) has stopped producing cold air as of '
                    . 'May 20, 2026. The unit runs normally (fan operational, no unusual sounds) but the thermostat '
                    . 'reads 30°C after 30 minutes of operation. Likely cause is refrigerant leak or clogged filters. '
                    . 'The unit was last serviced in November 2025. A replacement unit may be required if repairs '
                    . 'are not cost-effective. Vendor has been asked to provide a quotation.',
                'priority'         => MaintenancePriority::Normal,
                'status'           => MaintenanceStatus::InReview,
                'resolution_notes' => null,
                'resolved_at'      => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 5. IN PROGRESS – High | Broken playground equipment (Block 2 park)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B2-L01')->value('id'),
                'submitted_by'     => $admin->id,
                'assigned_to'      => $vendor->id,
                'category'         => MaintenanceCategory::CommonArea,
                'title'            => 'Playground – Broken Swing Set and Rusted Slide Rails',
                'description'      => 'The playground at the Block 2 pocket park has two reported hazards: '
                    . '(1) One of three swing chains has snapped, leaving a bare hook; a child nearly fell on '
                    . 'May 10, 2026. (2) The galvanized railings of the slide have developed sharp rust spots at '
                    . 'the joints. Both items pose injury risks to children. The HOA has temporarily cordoned off '
                    . 'the swing area with caution tape. Replacement swing chain set was ordered from PHINMA '
                    . 'Industrial Supply on May 17, 2026 (PO #2026-0517-01). Chain delivery expected May 26; '
                    . 'installation and rust treatment are scheduled for May 27–28.',
                'priority'         => MaintenancePriority::High,
                'status'           => MaintenanceStatus::InProgress,
                'resolution_notes' => null,
                'resolved_at'      => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 6. IN PROGRESS – Normal | Pothole on Rosal Street (Block 4)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B4-L02')->value('id'),
                'submitted_by'     => $residents['b4l02']->id,
                'assigned_to'      => $vendor->id,
                'category'         => MaintenanceCategory::Structural,
                'title'            => 'Large Pothole – Rosal Street in Front of B4-L02 to B4-L03',
                'description'      => 'A large pothole approximately 80 cm wide and 10 cm deep has formed on '
                    . 'Rosal Street directly in front of my unit. It was a small crack before the May rains but '
                    . 'has expanded significantly. Two (2) vehicles have already sustained wheel rim damage. '
                    . 'I request urgent road patching before more vehicles are affected. The HOA maintenance team '
                    . 'inspected on May 22 and confirmed it is within HOA road jurisdiction (not Biñan City DPWH). '
                    . 'Patching materials have been sourced and work is scheduled for May 28, 2026.',
                'priority'         => MaintenancePriority::Normal,
                'status'           => MaintenanceStatus::InProgress,
                'resolution_notes' => null,
                'resolved_at'      => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 7. RESOLVED – Normal | Broken pathway lighting (Block 1 entrance)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B1-L02')->value('id'),
                'submitted_by'     => $residents['b1l02']->id,
                'assigned_to'      => $vendor->id,
                'category'         => MaintenanceCategory::Electrical,
                'title'            => 'Pathway Lights Not Working – Sampaguita Street Entrance, Block 1',
                'description'      => 'Three (3) consecutive pathway light posts along Sampaguita Street '
                    . 'between the main gate and Block 1 have been non-operational for approximately 10 days. '
                    . 'This has created a dark stretch of road that is unsafe for pedestrians at night, '
                    . 'especially elderly residents and children. I have observed at least two pedestrians '
                    . 'stumbling on the uneven pavement in the darkness. Please prioritize this repair.',
                'priority'         => MaintenancePriority::Normal,
                'status'           => MaintenanceStatus::Resolved,
                'resolution_notes' => 'Electrician from R.B. Macaraeg General Services replaced all 3 faulty '
                    . 'photocell sensors and 2 burned ballast units on May 16, 2026. All lights are now fully '
                    . 'operational and tested. LED replacements were used instead of fluorescent for longer lifespan. '
                    . 'Total cost: ₱4,200.00 (parts + labor) charged to HOA maintenance budget.',
                'resolved_at'      => '2026-05-16 17:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 8. RESOLVED – Urgent | Water pipe burst (common area, Block 2)
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B2-L01')->value('id'),
                'submitted_by'     => $admin->id,
                'assigned_to'      => $vendor->id,
                'category'         => MaintenanceCategory::Plumbing,
                'title'            => 'Water Pipe Burst – HOA Main Line, Ilang-ilang Street (Block 2)',
                'description'      => 'An emergency call was received at 7:15 AM on May 5, 2026 reporting a '
                    . 'burst underground water pipe along Ilang-ilang Street (Block 2 main supply line). '
                    . 'Water was gushing from the road surface causing flooding on the street and affected '
                    . 'units B2-L01 to B2-L04. LAGUNA WATER was immediately notified to isolate the main valve. '
                    . 'This is an urgent repair – all Block 2 units currently have no water service.',
                'priority'         => MaintenancePriority::Urgent,
                'status'           => MaintenanceStatus::Resolved,
                'resolution_notes' => 'Emergency plumbing repair was completed by R.B. Macaraeg General Services '
                    . 'on May 5, 2026 (same day). A 3-meter section of 2-inch galvanized iron pipe was replaced '
                    . 'with HDPE pipe. LAGUNA WATER restored main supply at 6:30 PM. '
                    . 'Road surface was patched on May 6. Total emergency repair cost: ₱18,500.00 '
                    . '(labor ₱8,000 + materials ₱10,500). Sourced from HOA emergency maintenance fund.',
                'resolved_at'      => '2026-05-06 09:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 9. CLOSED – Low | Resident request outside HOA scope
            // ──────────────────────────────────────────────────────────────────
            [
                'property_id'      => Property::where('unit_number', 'B1-L01')->value('id'),
                'submitted_by'     => $residents['b1l01']->id,
                'assigned_to'      => null,
                'category'         => MaintenanceCategory::Other,
                'title'            => 'Request: Install Speed Bumps on Sampaguita Street',
                'description'      => 'I would like to request the installation of additional speed bumps on '
                    . 'Sampaguita Street. Vehicles (particularly delivery motorcycles and visitors) tend to '
                    . 'drive very fast past the Block 1 residences during the morning and evening rush. '
                    . 'There are children walking to the school bus pick-up area and this is a serious safety '
                    . 'concern. Please consider adding at least 2 more speed humps between the gate and Block 1.',
                'priority'         => MaintenancePriority::Low,
                'status'           => MaintenanceStatus::Closed,
                'resolution_notes' => 'This request has been logged and forwarded to the Board for inclusion '
                    . 'in the infrastructure agenda. However, installation of road features on subdivision roads '
                    . 'requires Biñan City DPWH approval and an engineering survey. A budget allocation will be '
                    . 'considered during the FY 2027 budget planning. Closing this ticket; the request will be '
                    . 'tracked under the Board\'s infrastructure improvement list.',
                'resolved_at'      => null,
            ],
        ];
    }
}
