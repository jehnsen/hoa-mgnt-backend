<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UtilityType;
use App\Models\Property;
use App\Models\User;
use App\Models\UtilityMeterReading;
use Illuminate\Database\Seeder;

/**
 * Seeds water meter readings for 5 Springdale Village residential units.
 *
 * All readings are for the May 2026 billing cycle (read on May 28, 2026).
 * LAGUNA WATER's residential rate schedule is used:
 *   - Fixed service charge:  ₱85.00/month
 *   - Lifeline (0–10 cu.m.): ₱18.70/cu.m.
 *   - Tier 2 (11–20 cu.m.): ₱24.50/cu.m.
 *   - Tier 3 (>20 cu.m.):   ₱31.20/cu.m.
 *
 * Readings are taken by the HOA admin on behalf of LAGUNA WATER for the
 * common-area sub-metering arrangement. Each unit has its own sub-meter.
 */
class UtilityMeterReadingSeeder extends Seeder
{
    public function run(): void
    {
        $reader = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();

        $readings = [
            // ── B1-L01 Carlos Reyes – Family of 4, moderate usage ───────────
            [
                'unit'             => 'B1-L01',
                'meter_number'     => 'LW-B1L01-0047',
                'previous_reading' => 1248.0000,
                'current_reading'  => 1267.0000,
                'reading_date'     => '2026-05-28',
                'rate_per_unit'    => 24.5000,
                'fixed_charge'     => 85.00,
                'notes'            => 'May 2026 reading. Consumption of 19 cu.m. – within normal range '
                    . 'for a family of 4. No leaks detected.',
            ],

            // ── B1-L02 Maria Grace Dela Cruz – Single professional ───────────
            [
                'unit'             => 'B1-L02',
                'meter_number'     => 'LW-B1L02-0048',
                'previous_reading' => 742.0000,
                'current_reading'  => 751.0000,
                'reading_date'     => '2026-05-28',
                'rate_per_unit'    => 18.7000,
                'fixed_charge'     => 85.00,
                'notes'            => 'May 2026 reading. Consumption of 9 cu.m. – lifeline tier. '
                    . 'Single-occupant unit; usage within expected range.',
            ],

            // ── B2-L01 Cristina Villanueva – Pool/garden heavy user ──────────
            [
                'unit'             => 'B2-L01',
                'meter_number'     => 'LW-B2L01-0061',
                'previous_reading' => 2015.0000,
                'current_reading'  => 2042.5000,
                'reading_date'     => '2026-05-28',
                'rate_per_unit'    => 31.2000,
                'fixed_charge'     => 85.00,
                'notes'            => 'May 2026 reading. Consumption of 27.5 cu.m. – Tier 3. '
                    . 'Higher usage attributed to garden watering during hot season and koi pond refill. '
                    . 'No leak flags from meter.',
            ],

            // ── B3-L01 Diana Navarro – Family of 3 ──────────────────────────
            [
                'unit'             => 'B3-L01',
                'meter_number'     => 'LW-B3L01-0077',
                'previous_reading' => 3310.0000,
                'current_reading'  => 3326.5000,
                'reading_date'     => '2026-05-28',
                'rate_per_unit'    => 24.5000,
                'fixed_charge'     => 85.00,
                'notes'            => 'May 2026 reading. Consumption of 16.5 cu.m. – Tier 2. '
                    . 'Normal household usage for a family of 3.',
            ],

            // ── B4-L01 Felicidad Espiritu – Extended family, high usage ──────
            [
                'unit'             => 'B4-L01',
                'meter_number'     => 'LW-B4L01-0091',
                'previous_reading' => 5122.0000,
                'current_reading'  => 5156.0000,
                'reading_date'     => '2026-05-28',
                'rate_per_unit'    => 31.2000,
                'fixed_charge'     => 85.00,
                'notes'            => 'May 2026 reading. Consumption of 34 cu.m. – Tier 3. '
                    . 'Extended family household with 6 occupants. Meter reading was cross-checked '
                    . 'against previous month; no anomaly detected. Admin advised homeowner to '
                    . 'consider installing a low-flow showerhead to reduce consumption.',
            ],
        ];

        foreach ($readings as $r) {
            $property    = Property::where('unit_number', $r['unit'])->firstOrFail();
            $consumption = round($r['current_reading'] - $r['previous_reading'], 4);

            UtilityMeterReading::create([
                'property_id'      => $property->id,
                'utility_type'     => UtilityType::Water,
                'meter_number'     => $r['meter_number'],
                'previous_reading' => $r['previous_reading'],
                'current_reading'  => $r['current_reading'],
                'consumption'      => $consumption,
                'reading_date'     => $r['reading_date'],
                'rate_per_unit'    => $r['rate_per_unit'],
                'fixed_charge'     => $r['fixed_charge'],
                'notes'            => $r['notes'],
                'read_by'          => $reader->id,
                'invoice_id'       => null,
            ]);
        }

        $this->command->info('  ✔ Seeded ' . UtilityMeterReading::count() . ' utility meter readings (May 2026 water billing cycle).');
    }
}
