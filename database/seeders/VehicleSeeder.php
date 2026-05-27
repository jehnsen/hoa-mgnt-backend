<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\VehicleType;
use App\Models\Property;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

/**
 * Seeds 14 registered vehicles for Springdale Village residents.
 *
 * Vehicle data uses realistic Philippine plate formats (new XXXNNN format)
 * and common vehicle makes/models popular in the Philippines.
 * Gate-pass sticker numbers follow the format: SV-YYYY-NNNN.
 */
class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'resident')->get()->keyBy('email');

        $vehicles = [
            // ── B1-L01 Carlos M. Reyes ────────────────────────────────────────────
            [
                'unit'          => 'B1-L01',
                'email'         => 'carlos.reyes@gmail.com',
                'plate_number'  => 'AAB 5678',
                'type'          => VehicleType::Car,
                'make'          => 'Toyota',
                'model'         => 'Fortuner 2.4 G 4x2 AT',
                'color'         => 'Pearl White',
                'year'          => 2022,
                'sticker_number'=> 'SV-2024-0001',
                'gate_pass_number' => 'GP-B1L01-01',
            ],
            [
                'unit'          => 'B1-L01',
                'email'         => 'carlos.reyes@gmail.com',
                'plate_number'  => 'CBB 2345',
                'type'          => VehicleType::Motorcycle,
                'make'          => 'Honda',
                'model'         => 'Click 125i',
                'color'         => 'Matte Black',
                'year'          => 2023,
                'sticker_number'=> 'SV-2024-0002',
                'gate_pass_number' => 'GP-B1L01-02',
            ],

            // ── B1-L02 Maria Grace A. Dela Cruz ──────────────────────────────────
            [
                'unit'          => 'B1-L02',
                'email'         => 'mariagrace.delacruz@gmail.com',
                'plate_number'  => 'AAC 1122',
                'type'          => VehicleType::Car,
                'make'          => 'Mitsubishi',
                'model'         => 'Montero Sport GLS 4x2 AT',
                'color'         => 'Red Metallic',
                'year'          => 2021,
                'sticker_number'=> 'SV-2024-0003',
                'gate_pass_number' => 'GP-B1L02-01',
            ],

            // ── B1-L05 Ramon D. Torres ────────────────────────────────────────────
            [
                'unit'          => 'B1-L05',
                'email'         => 'ramon.torres@outlook.com',
                'plate_number'  => 'ABD 9988',
                'type'          => VehicleType::Van,
                'make'          => 'Toyota',
                'model'         => 'Hi-Ace Commuter 3.0 MT',
                'color'         => 'Silver',
                'year'          => 2019,
                'sticker_number'=> 'SV-2024-0004',
                'gate_pass_number' => 'GP-B1L05-01',
            ],

            // ── B2-L01 Cristina P. Villanueva ─────────────────────────────────────
            [
                'unit'          => 'B2-L01',
                'email'         => 'cristina.villanueva@gmail.com',
                'plate_number'  => 'ACA 7766',
                'type'          => VehicleType::Car,
                'make'          => 'Honda',
                'model'         => 'City RS 1.5 CVT',
                'color'         => 'Lunar Silver Metallic',
                'year'          => 2024,
                'sticker_number'=> 'SV-2025-0005',
                'gate_pass_number' => 'GP-B2L01-01',
            ],

            // ── B2-L04 Roberto S. Castillo ────────────────────────────────────────
            [
                'unit'          => 'B2-L04',
                'email'         => 'roberto.castillo@gmail.com',
                'plate_number'  => 'ACB 3344',
                'type'          => VehicleType::Car,
                'make'          => 'Suzuki',
                'model'         => 'Ertiga GL 1.5 AT',
                'color'         => 'Oxford Blue',
                'year'          => 2023,
                'sticker_number'=> 'SV-2025-0006',
                'gate_pass_number' => 'GP-B2L04-01',
            ],

            // ── B3-L01 Diana F. Navarro ───────────────────────────────────────────
            [
                'unit'          => 'B3-L01',
                'email'         => 'diana.navarro@gmail.com',
                'plate_number'  => 'ACE 5500',
                'type'          => VehicleType::Car,
                'make'          => 'Hyundai',
                'model'         => 'Tucson 2.0 GLS AT 2WD',
                'color'         => 'Amazon Gray',
                'year'          => 2022,
                'sticker_number'=> 'SV-2025-0007',
                'gate_pass_number' => 'GP-B3L01-01',
            ],

            // ── B3-L02 Emmanuel V. Soriano ────────────────────────────────────────
            [
                'unit'          => 'B3-L02',
                'email'         => 'emmanuel.soriano@gmail.com',
                'plate_number'  => 'ADB 6677',
                'type'          => VehicleType::Car,
                'make'          => 'Ford',
                'model'         => 'Everest Titanium 2.0 Bi-Turbo 4WD AT',
                'color'         => 'Absolute Black',
                'year'          => 2023,
                'sticker_number'=> 'SV-2025-0008',
                'gate_pass_number' => 'GP-B3L02-01',
            ],
            [
                'unit'          => 'B3-L02',
                'email'         => 'emmanuel.soriano@gmail.com',
                'plate_number'  => 'DCA 1155',
                'type'          => VehicleType::Motorcycle,
                'make'          => 'Kawasaki',
                'model'         => 'Dominar 400',
                'color'         => 'Canyon Khaki Green',
                'year'          => 2022,
                'sticker_number'=> 'SV-2025-0009',
                'gate_pass_number' => 'GP-B3L02-02',
            ],

            // ── B4-L01 Felicidad Q. Espiritu ──────────────────────────────────────
            [
                'unit'          => 'B4-L01',
                'email'         => 'felicidad.espiritu@gmail.com',
                'plate_number'  => 'AEA 2233',
                'type'          => VehicleType::Car,
                'make'          => 'Kia',
                'model'         => 'Carnival SX 2.2 CRDi AT',
                'color'         => 'Snow White Pearl',
                'year'          => 2024,
                'sticker_number'=> 'SV-2025-0010',
                'gate_pass_number' => 'GP-B4L01-01',
            ],

            // ── B4-L03 Corazon R. Aguilar ─────────────────────────────────────────
            [
                'unit'          => 'B4-L03',
                'email'         => 'corazon.aguilar@gmail.com',
                'plate_number'  => 'AEC 8844',
                'type'          => VehicleType::Car,
                'make'          => 'Toyota',
                'model'         => 'Innova E 2.8 MT',
                'color'         => 'Super White',
                'year'          => 2020,
                'sticker_number'=> 'SV-2024-0011',
                'gate_pass_number' => 'GP-B4L03-01',
            ],

            // ── B5-L01 Benjamin A. Magno ──────────────────────────────────────────
            [
                'unit'          => 'B5-L01',
                'email'         => 'benjamin.magno@gmail.com',
                'plate_number'  => 'AFA 4477',
                'type'          => VehicleType::Car,
                'make'          => 'Nissan',
                'model'         => 'Navara EL King Cab 4x2 MT',
                'color'         => 'Blade Silver',
                'year'          => 2021,
                'sticker_number'=> 'SV-2024-0012',
                'gate_pass_number' => 'GP-B5L01-01',
            ],

            // ── B5-L02 Rosario C. Bersamin ────────────────────────────────────────
            [
                'unit'          => 'B5-L02',
                'email'         => 'rosario.bersamin@yahoo.com',
                'plate_number'  => 'AFB 6699',
                'type'          => VehicleType::Car,
                'make'          => 'Geely',
                'model'         => 'Okavango Luxury 1.5T AT',
                'color'         => 'Starry Black',
                'year'          => 2023,
                'sticker_number'=> 'SV-2025-0013',
                'gate_pass_number' => 'GP-B5L02-01',
            ],

            // ── B1-L03 Josephine B. Garcia ────────────────────────────────────────
            [
                'unit'          => 'B1-L03',
                'email'         => 'josephine.garcia@gmail.com',
                'plate_number'  => 'AGC 0011',
                'type'          => VehicleType::Car,
                'make'          => 'Toyota',
                'model'         => 'Vios XLE 1.3 CVT',
                'color'         => 'Burning Black',
                'year'          => 2023,
                'sticker_number'=> 'SV-2025-0014',
                'gate_pass_number' => 'GP-B1L03-01',
            ],
        ];

        foreach ($vehicles as $v) {
            $property = Property::where('unit_number', $v['unit'])->firstOrFail();
            $user     = $users[$v['email']];

            Vehicle::create([
                'property_id'      => $property->id,
                'registered_by'    => $user->id,
                'plate_number'     => $v['plate_number'],
                'type'             => $v['type'],
                'make'             => $v['make'],
                'model'            => $v['model'],
                'color'            => $v['color'],
                'year'             => $v['year'],
                'sticker_number'   => $v['sticker_number'],
                'gate_pass_number' => $v['gate_pass_number'],
                'is_active'        => true,
            ]);
        }

        $this->command->info('  ✔ Seeded ' . Vehicle::count() . ' registered vehicles across 10 properties.');
    }
}
