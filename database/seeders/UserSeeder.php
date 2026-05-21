<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the Springdale Village HOA staff and resident roster.
 *
 * Default password for all seeded accounts: Springdale@2026
 * (Change immediately after first login in any real environment.)
 */
class UserSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'Springdale@2026';

    public function run(): void
    {
        // ── 1. Super Administrator ─────────────────────────────────────────────
        User::create([
            'name'              => 'Springdale Village HOA',
            'email'             => 'admin@springdale-hoa.ph',
            'password'          => Hash::make(self::DEFAULT_PASSWORD),
            'role'              => UserRole::SuperAdmin,
            'phone'             => '+63 49 511 0001',
            'email_verified_at' => now(),
        ]);

        // ── 2. Board of Directors ──────────────────────────────────────────────
        $boardMembers = [
            [
                'name'  => 'Rodrigo B. Aquino',        // HOA President
                'email' => 'rodrigo.aquino@springdale-hoa.ph',
                'phone' => '+63 917 100 2001',
            ],
            [
                'name'  => 'Ma. Lourdes C. Santos',    // HOA Treasurer
                'email' => 'lourdes.santos@springdale-hoa.ph',
                'phone' => '+63 918 200 3002',
            ],
            [
                'name'  => 'Jose T. Bautista',         // HOA Secretary
                'email' => 'jose.bautista@springdale-hoa.ph',
                'phone' => '+63 919 300 4003',
            ],
        ];

        foreach ($boardMembers as $member) {
            User::create([
                ...$member,
                'password'          => Hash::make(self::DEFAULT_PASSWORD),
                'role'              => UserRole::BoardMember,
                'email_verified_at' => now(),
            ]);
        }

        // ── 3. Residents (mapped to specific units in the subdivision) ─────────
        // Index in this array maps to the slot order used in PropertyUserSeeder.
        $residents = [
            // Block 1 – Sampaguita Street
            ['name' => 'Carlos M. Reyes',          'email' => 'carlos.reyes@gmail.com',          'phone' => '+63 917 401 1001'],
            ['name' => 'Maria Grace A. Dela Cruz',  'email' => 'mariagrace.delacruz@gmail.com',   'phone' => '+63 918 402 2002'],
            ['name' => 'Eduardo R. Lopez',          'email' => 'eduardo.lopez@yahoo.com',          'phone' => '+63 919 403 3003'],
            ['name' => 'Josephine B. Garcia',       'email' => 'josephine.garcia@gmail.com',       'phone' => '+63 920 404 4004'],
            ['name' => 'Ramon D. Torres',           'email' => 'ramon.torres@outlook.com',         'phone' => '+63 921 405 5005'],

            // Block 2 – Ilang-ilang Street
            ['name' => 'Cristina P. Villanueva',   'email' => 'cristina.villanueva@gmail.com',   'phone' => '+63 917 406 6006'],
            ['name' => 'Antonio M. Hernandez',     'email' => 'antonio.hernandez@gmail.com',     'phone' => '+63 918 407 7007'],
            ['name' => 'Ana Lucia C. Mendoza',     'email' => 'analucia.mendoza@yahoo.com',      'phone' => '+63 919 408 8008'],
            ['name' => 'Roberto S. Castillo',      'email' => 'roberto.castillo@gmail.com',      'phone' => '+63 920 409 9009'],

            // Block 3 – Narra Street
            ['name' => 'Diana F. Navarro',         'email' => 'diana.navarro@gmail.com',         'phone' => '+63 921 410 0010'],
            ['name' => 'Emmanuel V. Soriano',      'email' => 'emmanuel.soriano@gmail.com',      'phone' => '+63 917 411 1011'],
            ['name' => 'Teresita L. Ramos',        'email' => 'teresita.ramos@yahoo.com',        'phone' => '+63 918 412 2012'],
            // B3-L04 is VACANT — no resident assigned

            // Block 4 – Rosal Street
            ['name' => 'Felicidad Q. Espiritu',    'email' => 'felicidad.espiritu@gmail.com',    'phone' => '+63 919 413 3013'],
            ['name' => 'Manuel T. Pascual',        'email' => 'manuel.pascual@outlook.com',      'phone' => '+63 920 414 4014'],
            ['name' => 'Corazon R. Aguilar',       'email' => 'corazon.aguilar@gmail.com',       'phone' => '+63 921 415 5015'],
            // B4-L04 is VACANT — no resident assigned

            // Block 5 – Santol Street
            ['name' => 'Benjamin A. Magno',        'email' => 'benjamin.magno@gmail.com',        'phone' => '+63 917 416 6016'],
            ['name' => 'Rosario C. Bersamin',      'email' => 'rosario.bersamin@yahoo.com',      'phone' => '+63 918 417 7017'],
            // B5-L03 is VACANT — no resident assigned

            // Residents without a current unit (recently moved in, on waiting list, or moved out)
            ['name' => 'Alicia V. Coronado',       'email' => 'alicia.coronado@gmail.com',       'phone' => '+63 919 418 8018'],
            ['name' => 'Danilo F. Buenaventura',   'email' => 'danilo.buenaventura@outlook.com', 'phone' => '+63 920 419 9019'],
        ];

        foreach ($residents as $resident) {
            User::create([
                ...$resident,
                'password'          => Hash::make(self::DEFAULT_PASSWORD),
                'role'              => UserRole::Resident,
                'email_verified_at' => now(),
            ]);
        }

        // ── 4. Vendors (contracted service providers) ──────────────────────────
        $vendors = [
            [
                'name'  => 'Jomar T. Lacsamana',   // Bayguard Security Services
                'email' => 'jomar.lacsamana@bayguard.ph',
                'phone' => '+63 915 500 6001',
            ],
            [
                'name'  => 'Rolando B. Macaraeg',  // HOA Maintenance & Landscaping
                'email' => 'rolando.macaraeg@springdale-hoa.ph',
                'phone' => '+63 916 501 7002',
            ],
        ];

        foreach ($vendors as $vendor) {
            User::create([
                ...$vendor,
                'password'          => Hash::make(self::DEFAULT_PASSWORD),
                'role'              => UserRole::Vendor,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('  ✔ Seeded ' . User::count() . ' users (1 SuperAdmin, 3 Board, 20 Residents, 2 Vendors).');
    }
}
