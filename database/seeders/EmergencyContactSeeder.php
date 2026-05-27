<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmergencyContact;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds emergency contacts for Springdale Village residents.
 *
 * Each resident has one or two emergency contacts with realistic Filipino names
 * and relationship types (spouse, parent, sibling, child). At least one contact
 * per resident is marked as primary.
 */
class EmergencyContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            // ── Carlos M. Reyes (B1-L01) ──────────────────────────────────────
            [
                'email'        => 'carlos.reyes@gmail.com',
                'name'         => 'Maria Elena B. Reyes',
                'relationship' => 'Spouse',
                'phone'        => '+63 917 501 1101',
                'email_addr'   => 'mariaelenareyes@gmail.com',
                'is_primary'   => true,
            ],
            [
                'email'        => 'carlos.reyes@gmail.com',
                'name'         => 'Renato M. Reyes Sr.',
                'relationship' => 'Father',
                'phone'        => '+63 920 501 1102',
                'email_addr'   => null,
                'is_primary'   => false,
            ],

            // ── Maria Grace A. Dela Cruz (B1-L02) ─────────────────────────────
            [
                'email'        => 'mariagrace.delacruz@gmail.com',
                'name'         => 'Roberto P. Dela Cruz',
                'relationship' => 'Spouse',
                'phone'        => '+63 918 502 2201',
                'email_addr'   => 'roberto.delacruz@gmail.com',
                'is_primary'   => true,
            ],
            [
                'email'        => 'mariagrace.delacruz@gmail.com',
                'name'         => 'Natividad A. Abrera',
                'relationship' => 'Mother',
                'phone'        => '+63 921 502 2202',
                'email_addr'   => null,
                'is_primary'   => false,
            ],

            // ── Eduardo R. Lopez (B1-L03) ──────────────────────────────────────
            [
                'email'        => 'eduardo.lopez@yahoo.com',
                'name'         => 'Carmen R. Lopez',
                'relationship' => 'Spouse',
                'phone'        => '+63 919 503 3301',
                'email_addr'   => 'carmen.lopez@yahoo.com',
                'is_primary'   => true,
            ],

            // ── Josephine B. Garcia (B1-L03) ──────────────────────────────────
            [
                'email'        => 'josephine.garcia@gmail.com',
                'name'         => 'Fernando G. Garcia Jr.',
                'relationship' => 'Son',
                'phone'        => '+63 920 504 4401',
                'email_addr'   => 'fgarciajr@gmail.com',
                'is_primary'   => true,
            ],

            // ── Ramon D. Torres (B1-L05) ──────────────────────────────────────
            [
                'email'        => 'ramon.torres@outlook.com',
                'name'         => 'Ligaya D. Torres',
                'relationship' => 'Spouse',
                'phone'        => '+63 921 505 5501',
                'email_addr'   => 'ligaya.torres@gmail.com',
                'is_primary'   => true,
            ],
            [
                'email'        => 'ramon.torres@outlook.com',
                'name'         => 'Elmer D. Torres',
                'relationship' => 'Brother',
                'phone'        => '+63 917 505 5502',
                'email_addr'   => null,
                'is_primary'   => false,
            ],

            // ── Cristina P. Villanueva (B2-L01) ───────────────────────────────
            [
                'email'        => 'cristina.villanueva@gmail.com',
                'name'         => 'Arnel B. Villanueva',
                'relationship' => 'Spouse',
                'phone'        => '+63 917 506 6601',
                'email_addr'   => 'arnel.villanueva@gmail.com',
                'is_primary'   => true,
            ],

            // ── Antonio M. Hernandez (B2-L02) ─────────────────────────────────
            [
                'email'        => 'antonio.hernandez@gmail.com',
                'name'         => 'Precilla M. Hernandez',
                'relationship' => 'Spouse',
                'phone'        => '+63 918 507 7701',
                'email_addr'   => 'precilla.hernandez@gmail.com',
                'is_primary'   => true,
            ],
            [
                'email'        => 'antonio.hernandez@gmail.com',
                'name'         => 'Gerardo M. Hernandez',
                'relationship' => 'Brother',
                'phone'        => '+63 921 507 7702',
                'email_addr'   => null,
                'is_primary'   => false,
            ],

            // ── Ana Lucia C. Mendoza (B2-L03) ─────────────────────────────────
            [
                'email'        => 'analucia.mendoza@yahoo.com',
                'name'         => 'Juanito C. Mendoza',
                'relationship' => 'Father',
                'phone'        => '+63 919 508 8801',
                'email_addr'   => null,
                'is_primary'   => true,
            ],

            // ── Roberto S. Castillo (B2-L04) ──────────────────────────────────
            [
                'email'        => 'roberto.castillo@gmail.com',
                'name'         => 'Rosalinda S. Castillo',
                'relationship' => 'Spouse',
                'phone'        => '+63 920 509 9901',
                'email_addr'   => 'rosalinda.castillo@gmail.com',
                'is_primary'   => true,
            ],

            // ── Diana F. Navarro (B3-L01) ─────────────────────────────────────
            [
                'email'        => 'diana.navarro@gmail.com',
                'name'         => 'Marco F. Navarro',
                'relationship' => 'Spouse',
                'phone'        => '+63 921 510 0011',
                'email_addr'   => 'marco.navarro@gmail.com',
                'is_primary'   => true,
            ],

            // ── Emmanuel V. Soriano (B3-L02) ──────────────────────────────────
            [
                'email'        => 'emmanuel.soriano@gmail.com',
                'name'         => 'Yvonne V. Soriano',
                'relationship' => 'Spouse',
                'phone'        => '+63 917 511 1101',
                'email_addr'   => 'yvonne.soriano@gmail.com',
                'is_primary'   => true,
            ],
            [
                'email'        => 'emmanuel.soriano@gmail.com',
                'name'         => 'Victor V. Soriano',
                'relationship' => 'Father',
                'phone'        => '+63 918 511 1102',
                'email_addr'   => null,
                'is_primary'   => false,
            ],

            // ── Teresita L. Ramos (B3-L03) ────────────────────────────────────
            [
                'email'        => 'teresita.ramos@yahoo.com',
                'name'         => 'Catalina L. Ramos-Buenaventura',
                'relationship' => 'Daughter',
                'phone'        => '+63 919 512 2201',
                'email_addr'   => 'catalina.buenaventura@gmail.com',
                'is_primary'   => true,
            ],

            // ── Felicidad Q. Espiritu (B4-L01) ────────────────────────────────
            [
                'email'        => 'felicidad.espiritu@gmail.com',
                'name'         => 'Alfredo Q. Espiritu Jr.',
                'relationship' => 'Son',
                'phone'        => '+63 920 513 3301',
                'email_addr'   => 'alfredo.espiritu@gmail.com',
                'is_primary'   => true,
            ],
            [
                'email'        => 'felicidad.espiritu@gmail.com',
                'name'         => 'Perla Q. Balagtas',
                'relationship' => 'Sister',
                'phone'        => '+63 921 513 3302',
                'email_addr'   => null,
                'is_primary'   => false,
            ],

            // ── Manuel T. Pascual (B4-L02) ────────────────────────────────────
            [
                'email'        => 'manuel.pascual@outlook.com',
                'name'         => 'Remedios T. Pascual',
                'relationship' => 'Spouse',
                'phone'        => '+63 917 514 4401',
                'email_addr'   => 'remedios.pascual@gmail.com',
                'is_primary'   => true,
            ],

            // ── Corazon R. Aguilar (B4-L03) ───────────────────────────────────
            [
                'email'        => 'corazon.aguilar@gmail.com',
                'name'         => 'Eulogio R. Aguilar',
                'relationship' => 'Spouse',
                'phone'        => '+63 918 515 5501',
                'email_addr'   => null,
                'is_primary'   => true,
            ],
            [
                'email'        => 'corazon.aguilar@gmail.com',
                'name'         => 'Maricel R. Aguilar-Dizon',
                'relationship' => 'Daughter',
                'phone'        => '+63 921 515 5502',
                'email_addr'   => 'maricel.dizon@gmail.com',
                'is_primary'   => false,
            ],

            // ── Benjamin A. Magno (B5-L01) ────────────────────────────────────
            [
                'email'        => 'benjamin.magno@gmail.com',
                'name'         => 'Florinda A. Magno',
                'relationship' => 'Spouse',
                'phone'        => '+63 919 516 6601',
                'email_addr'   => 'florinda.magno@gmail.com',
                'is_primary'   => true,
            ],

            // ── Rosario C. Bersamin (B5-L02) ──────────────────────────────────
            [
                'email'        => 'rosario.bersamin@yahoo.com',
                'name'         => 'Crisanto C. Bersamin',
                'relationship' => 'Son',
                'phone'        => '+63 920 517 7701',
                'email_addr'   => 'crisanto.bersamin@gmail.com',
                'is_primary'   => true,
            ],
        ];

        $users = User::where('role', 'resident')->get()->keyBy('email');

        foreach ($contacts as $c) {
            $user = $users[$c['email']];

            EmergencyContact::create([
                'user_id'      => $user->id,
                'name'         => $c['name'],
                'relationship' => $c['relationship'],
                'phone'        => $c['phone'],
                'email'        => $c['email_addr'],
                'is_primary'   => $c['is_primary'],
            ]);
        }

        $this->command->info('  ✔ Seeded ' . EmergencyContact::count() . ' emergency contacts across ' . count(array_unique(array_column($contacts, 'email'))) . ' residents.');
    }
}
