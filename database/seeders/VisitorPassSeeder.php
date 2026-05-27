<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use App\Models\VisitorPass;
use Illuminate\Database\Seeder;

/**
 * Seeds 5 visitor pass records for Springdale Village residents.
 *
 * Status distribution:
 *   Active/Unused (2) – upcoming visitors not yet arrived
 *   Used (2)          – visitor has checked in via the gate guard
 *   Expired (1)       – pass window lapsed without check-in
 *
 * Access codes follow the format: SVVP-YYYYMMDD-XXXX (alphanumeric suffix).
 */
class VisitorPassSeeder extends Seeder
{
    public function run(): void
    {
        $passes = [
            // ── 1. ACTIVE – B1-L01 Carlos Reyes expecting a plumber ──────────
            [
                'unit'          => 'B1-L01',
                'resident_email'=> 'carlos.reyes@gmail.com',
                'visitor_name'  => 'Ernesto P. Villafuerte',
                'vehicle_plate' => null,
                'expected_at'   => '2026-06-02 09:00:00',
                'expires_at'    => '2026-06-02 17:00:00',
                'purpose'       => 'Plumbing contractor – kitchen sink drain repair and kitchen faucet replacement. '
                    . 'Engaged via Anytimeplumbing.ph (Job Order #APH-2026-0531).',
                'access_code'   => 'SVP-A7K2',
                'is_used'       => false,
                'used_at'       => null,
            ],

            // ── 2. ACTIVE – B3-L02 Emmanuel Soriano expecting movers ─────────
            [
                'unit'          => 'B3-L02',
                'resident_email'=> 'emmanuel.soriano@gmail.com',
                'visitor_name'  => 'Ligtas Lipat Moving Services (3-man crew)',
                'vehicle_plate' => 'TBF 3301',
                'expected_at'   => '2026-06-03 07:00:00',
                'expires_at'    => '2026-06-03 18:00:00',
                'purpose'       => 'Move-out of household furniture and appliances. '
                    . 'Homeowner notified HOA office per House Rules Section 2.1 (24-hour advance notice for moving). '
                    . 'Inventory list submitted to HOA on May 31, 2026.',
                'access_code'   => 'SVP-B2M8',
                'is_used'       => false,
                'used_at'       => null,
            ],

            // ── 3. USED – B2-L01 Cristina Villanueva – family visitor ────────
            [
                'unit'          => 'B2-L01',
                'resident_email'=> 'cristina.villanueva@gmail.com',
                'visitor_name'  => 'Ariel B. Villanueva',
                'vehicle_plate' => 'BAE 2299',
                'expected_at'   => '2026-05-25 13:00:00',
                'expires_at'    => '2026-05-25 22:00:00',
                'purpose'       => "Family visit – brother-in-law attending Cristina's birthday celebration.",
                'access_code'   => 'SVP-C3F1',
                'is_used'       => true,
                'used_at'       => '2026-05-25 13:24:00',
            ],

            // ── 4. USED – B4-L01 Felicidad Espiritu – appliance delivery ─────
            [
                'unit'          => 'B4-L01',
                'resident_email'=> 'felicidad.espiritu@gmail.com',
                'visitor_name'  => 'Abenson Appliances Delivery (2-man crew)',
                'vehicle_plate' => 'DBF 5544',
                'expected_at'   => '2026-05-22 10:00:00',
                'expires_at'    => '2026-05-22 14:00:00',
                'purpose'       => 'LG refrigerator delivery and installation (Order No. ABN-20260521-88732). '
                    . 'Delivery crew permitted to bring items through the pedestrian gate with unit handcart.',
                'access_code'   => 'SVP-D4R9',
                'is_used'       => true,
                'used_at'       => '2026-05-22 10:38:00',
            ],

            // ── 5. EXPIRED (unused) – B5-L01 Benjamin Magno ─────────────────
            [
                'unit'          => 'B5-L01',
                'resident_email'=> 'benjamin.magno@gmail.com',
                'visitor_name'  => 'Remy R. Alcantara',
                'vehicle_plate' => null,
                'expected_at'   => '2026-05-20 15:00:00',
                'expires_at'    => '2026-05-20 21:00:00',
                'purpose'       => 'Personal guest – former colleague. Visitor did not arrive; pass lapsed unused.',
                'access_code'   => 'SVP-E5N3',
                'is_used'       => false,
                'used_at'       => null,
            ],
        ];

        foreach ($passes as $p) {
            $property = Property::where('unit_number', $p['unit'])->firstOrFail();
            $resident = User::where('email', $p['resident_email'])->firstOrFail();

            VisitorPass::create([
                'property_id'  => $property->id,
                'resident_id'  => $resident->id,
                'visitor_name' => $p['visitor_name'],
                'vehicle_plate'=> $p['vehicle_plate'],
                'expected_at'  => $p['expected_at'],
                'expires_at'   => $p['expires_at'],
                'purpose'      => $p['purpose'],
                'access_code'  => $p['access_code'],
                'is_used'      => $p['is_used'],
                'used_at'      => $p['used_at'],
            ]);
        }

        $used    = VisitorPass::where('is_used', true)->count();
        $unused  = VisitorPass::where('is_used', false)->count();

        $this->command->info("  ✔ Seeded " . VisitorPass::count() . " visitor passes ({$used} used, {$unused} unused).");
    }
}
