<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenanceFrequency;
use App\Enums\MaintenancePriority;
use App\Models\RecurringMaintenanceSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 6 recurring maintenance schedules for Springdale Village common areas.
 *
 * All schedules are property_id = null (common-area) and assigned to the HOA
 * maintenance vendor (Rolando Macaraeg). Frequencies and estimated costs are
 * based on typical Philippine subdivision maintenance contracts.
 */
class RecurringMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = User::where('email', 'rolando.macaraeg@springdale-hoa.ph')->firstOrFail();

        $schedules = [
            // ── 1. Swimming pool chemical treatment (Monthly) ────────────────
            [
                'property_id'        => null,
                'assigned_to'        => $vendor->id,
                'category'           => MaintenanceCategory::CommonArea,
                'title'              => 'Swimming Pool Chemical Treatment & Water Quality Check',
                'description'        => 'Monthly chemical balancing of pool water (pH, chlorine, alkalinity). '
                    . 'Includes backwashing of sand filter, cleaning of skimmer baskets, and brushing pool walls. '
                    . 'Water samples to be tested against DOH swimming pool standards. '
                    . 'Contractor: R&B Maintenance. Chemical supplier: PoolMart Laguna.',
                'priority'           => MaintenancePriority::High,
                'frequency'          => MaintenanceFrequency::Monthly,
                'frequency_interval' => 1,
                'estimated_cost'     => 3500.00,
                'next_due_at'        => '2026-06-05',
                'last_run_at'        => '2026-05-05',
                'is_active'          => true,
            ],

            // ── 2. Fire extinguisher inspection & recharging (Quarterly) ────
            [
                'property_id'        => null,
                'assigned_to'        => $vendor->id,
                'category'           => MaintenanceCategory::CommonArea,
                'title'              => 'Fire Extinguisher Inspection, Testing & Recharging',
                'description'        => 'Quarterly inspection of all 14 dry chemical fire extinguishers '
                    . 'installed in the clubhouse, guardhouse, and common-area hallways. '
                    . 'Includes pressure gauge check, pin and seal inspection, and recharging of any unit '
                    . 'below 85% capacity. Certification tag update required per BFP (Bureau of Fire Protection) '
                    . 'guidelines. Any expired or defective units to be replaced and reported to HOA admin.',
                'priority'           => MaintenancePriority::High,
                'frequency'          => MaintenanceFrequency::Quarterly,
                'frequency_interval' => 1,
                'estimated_cost'     => 2800.00,
                'next_due_at'        => '2026-07-01',
                'last_run_at'        => '2026-04-01',
                'is_active'          => true,
            ],

            // ── 3. Pest control / termite treatment (Monthly) ───────────────
            [
                'property_id'        => null,
                'assigned_to'        => $vendor->id,
                'category'           => MaintenanceCategory::CommonArea,
                'title'              => 'Common Area Pest Control & Mosquito Fogging',
                'description'        => 'Monthly general pest control (insect spray) of the clubhouse, '
                    . 'covered walkways, and common restrooms, plus anti-mosquito fogging of all common-area '
                    . 'gardens and the perimeter drainage canals. '
                    . 'Dengue prevention fogging to follow DOH recommended schedule. '
                    . 'Contractor must use WHO-approved chemicals. Residents to be notified 24 hours in advance. '
                    . 'Service provider: Pestex Philippines (Laguna branch).',
                'priority'           => MaintenancePriority::Normal,
                'frequency'          => MaintenanceFrequency::Monthly,
                'frequency_interval' => 1,
                'estimated_cost'     => 4200.00,
                'next_due_at'        => '2026-06-15',
                'last_run_at'        => '2026-05-15',
                'is_active'          => true,
            ],

            // ── 4. CCTV system cleaning & functional check (Quarterly) ──────
            [
                'property_id'        => null,
                'assigned_to'        => $vendor->id,
                'category'           => MaintenanceCategory::Electrical,
                'title'              => 'CCTV System Preventive Maintenance & NVR Backup Check',
                'description'        => 'Quarterly preventive maintenance of the 12-camera CCTV system. '
                    . 'Includes lens cleaning, housing inspection, cable tightening, IR night-vision test, '
                    . 'and NVR (Network Video Recorder) hard-drive health check. '
                    . '60-day rolling footage retention to be verified. Any camera with degraded image quality '
                    . 'or pointing misalignment to be corrected. Guard monitoring station UPS battery to be tested. '
                    . 'Service contact: TechVision Security Systems, Sta. Rosa, Laguna.',
                'priority'           => MaintenancePriority::Normal,
                'frequency'          => MaintenanceFrequency::Quarterly,
                'frequency_interval' => 1,
                'estimated_cost'     => 3200.00,
                'next_due_at'        => '2026-07-15',
                'last_run_at'        => '2026-04-15',
                'is_active'          => true,
            ],

            // ── 5. Common-area lawn mowing & shrub trimming (Weekly) ────────
            [
                'property_id'        => null,
                'assigned_to'        => $vendor->id,
                'category'           => MaintenanceCategory::Landscaping,
                'title'              => 'Common Area Lawn Mowing, Trimming & Sweeping',
                'description'        => 'Weekly mowing of all common-area lawns (estimated 1,200 sqm total), '
                    . 'trimming of hedges and ornamental shrubs along Sampaguita, Ilang-ilang, Narra, Rosal, '
                    . 'and Santol Streets, and sweeping/blowing of concrete pathways and gutters. '
                    . 'Clippings and debris to be bagged and disposed of via MENRO-Biñan scheduled pickup. '
                    . 'Two-person crew required; work days: every Monday and Thursday.',
                'priority'           => MaintenancePriority::Normal,
                'frequency'          => MaintenanceFrequency::Weekly,
                'frequency_interval' => 1,
                'estimated_cost'     => 2000.00,
                'next_due_at'        => '2026-06-02',
                'last_run_at'        => '2026-05-26',
                'is_active'          => true,
            ],

            // ── 6. Perimeter wall repainting (Annually) ─────────────────────
            [
                'property_id'        => null,
                'assigned_to'        => $vendor->id,
                'category'           => MaintenanceCategory::Structural,
                'title'              => 'Perimeter Wall & Gate Repainting',
                'description'        => 'Annual repainting of the approximately 480-linear-meter perimeter '
                    . 'concrete hollow block wall and main entry/exit gate metal works. '
                    . 'Surface preparation includes pressure washing, crack sealing with hydraulic cement, '
                    . 'and application of masonry waterproof primer. Topcoat: Boysen Permacoat Latex in '
                    . 'approved HOA color (Ivory White #B-701 with Forest Green #B-625 accents). '
                    . 'Main gate metalworks to receive alkyd enamel (Gloss Black). '
                    . 'Scheduled annually during the dry season (March–May).',
                'priority'           => MaintenancePriority::Low,
                'frequency'          => MaintenanceFrequency::Annually,
                'frequency_interval' => 1,
                'estimated_cost'     => 85000.00,
                'next_due_at'        => '2027-03-01',
                'last_run_at'        => '2026-03-10',
                'is_active'          => true,
            ],
        ];

        foreach ($schedules as $data) {
            RecurringMaintenanceSchedule::create($data);
        }

        $this->command->info('  ✔ Seeded ' . RecurringMaintenanceSchedule::count() . ' recurring maintenance schedules.');
    }
}
