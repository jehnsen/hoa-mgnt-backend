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
            AmenitySeeder::class,          // must run before AmenityBookingSeeder & AmenityBlackoutSeeder
            AmenityBookingSeeder::class,
            DocumentSeeder::class,
            MeetingSeeder::class,          // includes votes and responses inline; must run before MeetingProxySeeder

            // ── Governance ───────────────────────────────────────────────────
            VendorProfileSeeder::class,    // depends on vendor users
            BoardPositionSeeder::class,    // depends on board member users
            CommitteeSeeder::class,        // depends on board + resident users
            BoardElectionSeeder::class,    // depends on resident users

            // ── Resident assets ───────────────────────────────────────────────
            VehicleSeeder::class,          // depends on properties + resident users
            PetSeeder::class,              // depends on properties + resident users
            EmergencyContactSeeder::class, // depends on resident users

            // ── Operations ───────────────────────────────────────────────────
            RecurringMaintenanceSeeder::class,  // depends on vendor users
            AmenityBlackoutSeeder::class,       // depends on AmenitySeeder
            VisitorPassSeeder::class,           // depends on properties + resident users
            MeetingProxySeeder::class,          // depends on MeetingSeeder + resident users

            // ── Compliance & Finance ─────────────────────────────────────────
            ViolationAppealSeeder::class,       // depends on ViolationSeeder (Appealed status)
            ClearanceSeeder::class,             // depends on properties + board users
            BudgetSeeder::class,
            UtilityMeterReadingSeeder::class,   // depends on properties + admin user

            // ── Audit trail (must run last — depends on all entity seeders) ───
            AuditLogSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✔  All done. Default password for all accounts: Springdale@2026');
        $this->command->info('');
    }
}
