<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PetType;
use App\Models\Pet;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 11 registered pets for Springdale Village residents.
 *
 * All pets are registered with vaccination records as required by the
 * HOA Pet Policy (Section 9) and the Biñan City Animal Welfare Ordinance.
 * Registration fee: ₱150.00 per pet per year.
 */
class PetSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'resident')->get()->keyBy('email');

        $pets = [
            // ── B1-L01 Carlos M. Reyes ────────────────────────────────────────────
            [
                'unit'               => 'B1-L01',
                'email'              => 'carlos.reyes@gmail.com',
                'name'               => 'Pogi',
                'type'               => PetType::Dog,
                'breed'              => 'Labrador Retriever',
                'color'              => 'Golden Yellow',
                'is_vaccinated'      => true,
                'vaccination_record' => 'Dr. Maricar Villanueva, Biñan Vet Clinic. '
                    . 'Anti-rabies: 2026-03-10 (valid until 2027-03-10). '
                    . 'DHPP booster: 2025-09-15.',
                'registration_fee'   => 150.00,
            ],
            [
                'unit'               => 'B1-L01',
                'email'              => 'carlos.reyes@gmail.com',
                'name'               => 'Miming',
                'type'               => PetType::Cat,
                'breed'              => 'Persian Mix',
                'color'              => 'White and Gray',
                'is_vaccinated'      => true,
                'vaccination_record' => 'Dr. Maricar Villanueva, Biñan Vet Clinic. '
                    . 'Feline FVRCP: 2025-11-20. Anti-rabies: 2025-11-20 (valid until 2026-11-20).',
                'registration_fee'   => 150.00,
            ],

            // ── B1-L03 Josephine B. Garcia ────────────────────────────────────────
            [
                'unit'               => 'B1-L03',
                'email'              => 'josephine.garcia@gmail.com',
                'name'               => 'Brownie',
                'type'               => PetType::Dog,
                'breed'              => 'Aspin (Asong Pilipino)',
                'color'              => 'Brown',
                'is_vaccinated'      => true,
                'vaccination_record' => 'City Vet – Biñan City Hall. '
                    . 'Anti-rabies (Rabisin): 2026-01-25 (valid until 2027-01-25).',
                'registration_fee'   => 150.00,
            ],

            // ── B2-L01 Cristina P. Villanueva ─────────────────────────────────────
            [
                'unit'               => 'B2-L01',
                'email'              => 'cristina.villanueva@gmail.com',
                'name'               => 'Luna',
                'type'               => PetType::Dog,
                'breed'              => 'Shih Tzu',
                'color'              => 'White with Brown patches',
                'is_vaccinated'      => true,
                'vaccination_record' => 'PetMed Animal Clinic, Biñan. '
                    . 'Anti-rabies: 2026-02-14 (valid until 2027-02-14). '
                    . 'DHPP: 2025-08-14.',
                'registration_fee'   => 150.00,
            ],
            [
                'unit'               => 'B2-L01',
                'email'              => 'cristina.villanueva@gmail.com',
                'name'               => 'Koi Set',
                'type'               => PetType::Fish,
                'breed'              => 'Japanese Koi (Nishikigoi)',
                'color'              => 'Red, White, Black',
                'is_vaccinated'      => false,
                'vaccination_record' => null,
                'registration_fee'   => 150.00,
            ],

            // ── B3-L01 Diana F. Navarro ───────────────────────────────────────────
            [
                'unit'               => 'B3-L01',
                'email'              => 'diana.navarro@gmail.com',
                'name'               => 'Chico',
                'type'               => PetType::Dog,
                'breed'              => 'Poodle',
                'color'              => 'Apricot',
                'is_vaccinated'      => true,
                'vaccination_record' => 'Happy Paws Vet, Sta. Rosa. '
                    . 'Anti-rabies: 2026-04-05 (valid until 2027-04-05). '
                    . 'DHPP + Bordetella: 2025-10-05.',
                'registration_fee'   => 150.00,
            ],

            // ── B3-L03 Teresita L. Ramos ──────────────────────────────────────────
            [
                'unit'               => 'B3-L03',
                'email'              => 'teresita.ramos@yahoo.com',
                'name'               => 'Tweety',
                'type'               => PetType::Bird,
                'breed'              => 'African Grey Parrot',
                'color'              => 'Gray with Red Tail',
                'is_vaccinated'      => false,
                'vaccination_record' => null,
                'registration_fee'   => 150.00,
            ],
            [
                'unit'               => 'B3-L03',
                'email'              => 'teresita.ramos@yahoo.com',
                'name'               => 'Fluffy',
                'type'               => PetType::Cat,
                'breed'              => 'Maine Coon',
                'color'              => 'Brown Tabby',
                'is_vaccinated'      => true,
                'vaccination_record' => 'City Vet – Biñan City Hall. '
                    . 'Anti-rabies (Nobivac Rabies): 2026-03-20 (valid until 2027-03-20).',
                'registration_fee'   => 150.00,
            ],

            // ── B4-L01 Felicidad Q. Espiritu ──────────────────────────────────────
            [
                'unit'               => 'B4-L01',
                'email'              => 'felicidad.espiritu@gmail.com',
                'name'               => 'Coco',
                'type'               => PetType::Dog,
                'breed'              => 'Golden Retriever',
                'color'              => 'Golden',
                'is_vaccinated'      => true,
                'vaccination_record' => 'PetMed Animal Clinic, Biñan. '
                    . 'Anti-rabies: 2026-01-08 (valid until 2027-01-08). '
                    . 'DHPP + Leptospirosis: 2025-07-08.',
                'registration_fee'   => 150.00,
            ],

            // ── B5-L01 Benjamin A. Magno ──────────────────────────────────────────
            [
                'unit'               => 'B5-L01',
                'email'              => 'benjamin.magno@gmail.com',
                'name'               => 'Rex',
                'type'               => PetType::Dog,
                'breed'              => 'German Shepherd',
                'color'              => 'Black and Tan',
                'is_vaccinated'      => true,
                'vaccination_record' => 'Bayvet Animal Hospital, Biñan. '
                    . 'Anti-rabies (Imrab): 2026-02-28 (valid until 2027-02-28). '
                    . 'DHPP: 2025-08-28. Guard dog — leash/muzzle required outside the lot.',
                'registration_fee'   => 150.00,
            ],

            // ── B5-L02 Rosario C. Bersamin ────────────────────────────────────────
            [
                'unit'               => 'B5-L02',
                'email'              => 'rosario.bersamin@yahoo.com',
                'name'               => 'Nemo',
                'type'               => PetType::Fish,
                'breed'              => 'Clownfish (Ornamental)',
                'color'              => 'Orange and White',
                'is_vaccinated'      => false,
                'vaccination_record' => null,
                'registration_fee'   => 150.00,
            ],
        ];

        foreach ($pets as $p) {
            $property = Property::where('unit_number', $p['unit'])->firstOrFail();
            $user     = $users[$p['email']];

            Pet::create([
                'property_id'        => $property->id,
                'registered_by'      => $user->id,
                'name'               => $p['name'],
                'type'               => $p['type'],
                'breed'              => $p['breed'],
                'color'              => $p['color'],
                'is_vaccinated'      => $p['is_vaccinated'],
                'vaccination_record' => $p['vaccination_record'],
                'registration_fee'   => $p['registration_fee'],
                'is_active'          => true,
            ]);
        }

        $this->command->info('  ✔ Seeded ' . Pet::count() . ' registered pets.');
    }
}
