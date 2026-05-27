<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Database\Seeder;

/**
 * Seeds vendor profiles for the two vendor users seeded by UserSeeder.
 *
 * Both profiles are linked to existing User records with role = vendor.
 * VendorProfile stores the company-level details; User stores login credentials.
 */
class VendorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $jomar   = User::where('email', 'jomar.lacsamana@bayguard.ph')->firstOrFail();
        $rolando = User::where('email', 'rolando.macaraeg@springdale-hoa.ph')->firstOrFail();

        VendorProfile::create([
            'user_id'        => $jomar->id,
            'company_name'   => 'Bayguard Security Services',
            'service_type'   => 'security',
            'license_number' => 'PNP-SSD-2024-BG-001187',
            'is_active'      => true,
            'notes'          => 'Accredited security agency since 2021. Provides 24/7 roving guards '
                . 'and gate access control for Springdale Village. Renewed contract FY 2026–2027. '
                . 'Contact: +63 915 500 6001.',
        ]);

        VendorProfile::create([
            'user_id'        => $rolando->id,
            'company_name'   => 'R&B Maintenance & Landscaping Services',
            'service_type'   => 'general_maintenance',
            'license_number' => 'DTI-BN-2022-RL-004452',
            'is_active'      => true,
            'notes'          => 'General contractor for HOA common-area maintenance, landscaping, '
                . 'minor civil works, and plumbing. DTI-registered sole proprietorship. '
                . 'Exclusive HOA maintenance contractor since January 2023. '
                . 'Contact: +63 916 501 7002.',
        ]);

        $this->command->info('  ✔ Seeded ' . VendorProfile::count() . ' vendor profiles.');
    }
}
