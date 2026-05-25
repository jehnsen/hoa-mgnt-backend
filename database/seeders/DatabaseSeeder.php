<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Springdale Village HOA – Master Seeder
 *
 * Execution order is intentional: foreign-key constraints require parents
 * before children. New feature seeders run after core data is established.
 *
 * Run with:
 *   php artisan migrate:fresh && php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════╗');
        $this->command->info('║  Springdale Village HOA – Seeding Sample Data        ║');
        $this->command->info('║  Brgy. San Antonio, Biñan City, Laguna 4024          ║');
        $this->command->info('╚══════════════════════════════════════════════════════╝');
        $this->command->info('');

        $this->call([
            // ── Core (must run first — others depend on users & properties) ───
            UserSeeder::class,
            PropertySeeder::class,
            PropertyUserSeeder::class,

            // ── Financial ────────────────────────────────────────────────────
            InvoiceSeeder::class,
            PaymentSeeder::class,

            // ── Violations ───────────────────────────────────────────────────
            ViolationSeeder::class,

            // ── New features ─────────────────────────────────────────────────
            AnnouncementSeeder::class,
            MaintenanceRequestSeeder::class,
            AmenitySeeder::class,          // must run before AmenityBookingSeeder
            AmenityBookingSeeder::class,
            DocumentSeeder::class,
            MeetingSeeder::class,          // includes votes and responses inline
        ]);

        $this->command->info('');
        $this->command->info('✔  All done. Default password for all accounts: Springdale@2026');
        $this->command->info('');
    }
}
