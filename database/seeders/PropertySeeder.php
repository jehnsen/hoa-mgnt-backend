<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

/**
 * Seeds 20 residential units inside Springdale Village,
 * Brgy. San Antonio, Biñan City, Laguna 4024.
 *
 * Layout:
 *  - Block 1 │ Sampaguita Street  │ 5 units (3 single-family, 2 townhouse)
 *  - Block 2 │ Ilang-ilang Street │ 4 units (2 single-family, 2 townhouse)
 *  - Block 3 │ Narra Street       │ 4 units (2 single-family, 2 townhouse)
 *  - Block 4 │ Rosal Street       │ 4 units (2 single-family, 2 townhouse)
 *  - Block 5 │ Santol Street      │ 3 units (1 single-family, 2 townhouse)
 *
 * Monthly dues:
 *  - single_family  → ₱ 1,200.00 / month
 *  - townhouse      → ₱   800.00 / month
 *
 * Late fee rate: 2% of base amount per overdue period (standard PH HOA rate).
 */
class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // ─── Block 1 – Sampaguita Street ───────────────────────────────────
            [
                'unit_number'    => 'B1-L01',
                'block'          => '1',
                'street_address' => '1 Sampaguita Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B1-L02',
                'block'          => '1',
                'street_address' => '2 Sampaguita Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B1-L03',
                'block'          => '1',
                'street_address' => '3 Sampaguita Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B1-L04',
                'block'          => '1',
                'street_address' => '4 Sampaguita Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B1-L05',
                'block'          => '1',
                'street_address' => '5 Sampaguita Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],

            // ─── Block 2 – Ilang-ilang Street ──────────────────────────────────
            [
                'unit_number'    => 'B2-L01',
                'block'          => '2',
                'street_address' => '1 Ilang-ilang Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B2-L02',
                'block'          => '2',
                'street_address' => '2 Ilang-ilang Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B2-L03',
                'block'          => '2',
                'street_address' => '3 Ilang-ilang Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B2-L04',
                'block'          => '2',
                'street_address' => '4 Ilang-ilang Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],

            // ─── Block 3 – Narra Street ─────────────────────────────────────────
            [
                'unit_number'    => 'B3-L01',
                'block'          => '3',
                'street_address' => '1 Narra Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B3-L02',
                'block'          => '3',
                'street_address' => '2 Narra Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B3-L03',
                'block'          => '3',
                'street_address' => '3 Narra Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B3-L04',
                'block'          => '3',
                'street_address' => '4 Narra Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
                'is_active'      => true,   // unit is active; currently unoccupied
            ],

            // ─── Block 4 – Rosal Street ─────────────────────────────────────────
            [
                'unit_number'    => 'B4-L01',
                'block'          => '4',
                'street_address' => '1 Rosal Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B4-L02',
                'block'          => '4',
                'street_address' => '2 Rosal Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B4-L03',
                'block'          => '4',
                'street_address' => '3 Rosal Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B4-L04',
                'block'          => '4',
                'street_address' => '4 Rosal Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
                'is_active'      => true,   // unit is active; currently unoccupied
            ],

            // ─── Block 5 – Santol Street ────────────────────────────────────────
            [
                'unit_number'    => 'B5-L01',
                'block'          => '5',
                'street_address' => '1 Santol Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B5-L02',
                'block'          => '5',
                'street_address' => '2 Santol Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'townhouse',
                'monthly_dues'   => 800.00,
                'late_fee_rate'  => 0.0200,
            ],
            [
                'unit_number'    => 'B5-L03',
                'block'          => '5',
                'street_address' => '3 Santol Street, Springdale Village, Brgy. San Antonio, Biñan City, Laguna 4024',
                'type'           => 'single_family',
                'monthly_dues'   => 1200.00,
                'late_fee_rate'  => 0.0200,
                'is_active'      => true,   // unit is active; currently unoccupied
            ],
        ];

        foreach ($units as $unit) {
            Property::create([
                'is_active' => true,
                ...$unit,
            ]);
        }

        $this->command->info('  ✔ Seeded ' . Property::count() . ' properties across 5 blocks (17 occupied, 3 vacant).');
    }
}
