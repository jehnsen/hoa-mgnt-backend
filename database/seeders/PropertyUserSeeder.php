<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Links residents to their respective units via the property_user pivot table.
 *
 * 17 of 20 units are occupied (B3-L04, B4-L04, B5-L03 are currently vacant).
 * move_in_at dates are set to realistic dates during 2022–2024.
 * is_primary_resident = true marks the primary account holder per unit.
 */
class PropertyUserSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Format:
         * [unit_number, resident_email, move_in_at, is_primary_resident]
         */
        $assignments = [
            // Block 1 – Sampaguita Street
            ['B1-L01', 'carlos.reyes@gmail.com',           '2022-03-15', true],
            ['B1-L02', 'mariagrace.delacruz@gmail.com',    '2021-07-01', true],
            ['B1-L03', 'eduardo.lopez@yahoo.com',          '2023-01-10', true],
            ['B1-L04', 'josephine.garcia@gmail.com',       '2022-09-20', true],
            ['B1-L05', 'ramon.torres@outlook.com',         '2020-11-05', true],

            // Block 2 – Ilang-ilang Street
            ['B2-L01', 'cristina.villanueva@gmail.com',    '2021-04-18', true],
            ['B2-L02', 'antonio.hernandez@gmail.com',      '2023-06-01', true],
            ['B2-L03', 'analucia.mendoza@yahoo.com',       '2022-12-03', true],
            ['B2-L04', 'roberto.castillo@gmail.com',       '2020-08-22', true],

            // Block 3 – Narra Street
            ['B3-L01', 'diana.navarro@gmail.com',          '2021-02-14', true],
            ['B3-L02', 'emmanuel.soriano@gmail.com',       '2023-09-01', true],
            ['B3-L03', 'teresita.ramos@yahoo.com',         '2022-05-30', true],
            // B3-L04 → VACANT (no assignment)

            // Block 4 – Rosal Street
            ['B4-L01', 'felicidad.espiritu@gmail.com',     '2021-11-11', true],
            ['B4-L02', 'manuel.pascual@outlook.com',       '2024-01-08', true],
            ['B4-L03', 'corazon.aguilar@gmail.com',        '2022-07-25', true],
            // B4-L04 → VACANT (no assignment)

            // Block 5 – Santol Street
            ['B5-L01', 'benjamin.magno@gmail.com',         '2023-03-17', true],
            ['B5-L02', 'rosario.bersamin@yahoo.com',       '2021-10-09', true],
            // B5-L03 → VACANT (no assignment)
        ];

        foreach ($assignments as [$unitNumber, $email, $moveInAt, $isPrimary]) {
            $property = Property::where('unit_number', $unitNumber)->firstOrFail();
            $resident = User::where('email', $email)->firstOrFail();

            $property->residents()->attach($resident->id, [
                'move_in_at'         => $moveInAt,
                'move_out_at'        => null,
                'is_primary_resident' => $isPrimary,
            ]);
        }

        $this->command->info('  ✔ Linked 17 residents to their units (B3-L04, B4-L04, B5-L03 remain vacant).');
    }
}
