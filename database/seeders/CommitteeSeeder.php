<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CommitteeRole;
use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 4 active HOA committees with realistic membership rosters.
 *
 * Each committee has a board-member chair and two to three resident members,
 * reflecting the typical composition of Philippine subdivision committees
 * under RA 9904 (Magna Carta for Homeowners).
 */
class CommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $aquino   = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $santos   = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();
        $bautista = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();

        $users = User::where('role', 'resident')->get()->keyBy('email');

        // ── 1. Safety & Security Committee ───────────────────────────────────────
        $security = Committee::create([
            'name'        => 'Safety & Security Committee',
            'description' => 'Oversees implementation and improvement of HOA security protocols, gate access '
                . 'management, CCTV operations, emergency response planning, and coordination with Bayguard '
                . 'Security Services and Barangay San Antonio Tanod. Reports directly to the Board President.',
            'is_active'   => true,
        ]);

        CommitteeMember::create([
            'committee_id' => $security->id,
            'user_id'      => $bautista->id,
            'role'         => CommitteeRole::Chair,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $security->id,
            'user_id'      => $users['carlos.reyes@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $security->id,
            'user_id'      => $users['benjamin.magno@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $security->id,
            'user_id'      => $users['manuel.pascual@outlook.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2025-01-10',
        ]);

        // ── 2. Finance & Audit Committee ─────────────────────────────────────────
        $finance = Committee::create([
            'name'        => 'Finance & Audit Committee',
            'description' => 'Reviews monthly financial statements, audits HOA collections and disbursements, '
                . 'recommends the annual budget to the Board, and ensures compliance with HLURB/DHSUD '
                . 'financial reporting requirements. Chaired by the HOA Treasurer.',
            'is_active'   => true,
        ]);

        CommitteeMember::create([
            'committee_id' => $finance->id,
            'user_id'      => $santos->id,
            'role'         => CommitteeRole::Chair,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $finance->id,
            'user_id'      => $users['diana.navarro@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $finance->id,
            'user_id'      => $users['felicidad.espiritu@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);

        // ── 3. Landscaping & Environment Committee ────────────────────────────────
        $landscaping = Committee::create([
            'name'        => 'Landscaping & Environment Committee',
            'description' => 'Manages the beautification and greening program of Springdale Village. '
                . 'Responsibilities include tree-planting drives, enforcement of the no-open-burning rule, '
                . 'scheduling quarterly community clean-up days, and coordination with R&B Maintenance for '
                . 'common-area lawn care and plant upkeep.',
            'is_active'   => true,
        ]);

        CommitteeMember::create([
            'committee_id' => $landscaping->id,
            'user_id'      => $aquino->id,
            'role'         => CommitteeRole::Chair,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $landscaping->id,
            'user_id'      => $users['teresita.ramos@yahoo.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $landscaping->id,
            'user_id'      => $users['corazon.aguilar@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $landscaping->id,
            'user_id'      => $users['diana.navarro@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2025-03-01',
        ]);

        // ── 4. Architectural & Construction Committee (ACC) ───────────────────────
        $acc = Committee::create([
            'name'        => 'Architectural & Construction Committee',
            'description' => 'Reviews and approves all renovation, construction, and modification permit '
                . 'applications submitted by homeowners. Ensures compliance with the HOA Deed of Restrictions, '
                . 'Biñan City building ordinances, and DHSUD subdivision standards before issuance of HOA '
                . 'Renovation Clearance. Conducts site inspections as needed.',
            'is_active'   => true,
        ]);

        CommitteeMember::create([
            'committee_id' => $acc->id,
            'user_id'      => $aquino->id,
            'role'         => CommitteeRole::Chair,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $acc->id,
            'user_id'      => $users['eduardo.lopez@yahoo.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);
        CommitteeMember::create([
            'committee_id' => $acc->id,
            'user_id'      => $users['antonio.hernandez@gmail.com']->id,
            'role'         => CommitteeRole::Member,
            'joined_at'    => '2024-07-15',
        ]);

        $this->command->info('  ✔ Seeded ' . Committee::count() . ' committees with ' . CommitteeMember::count() . ' total members.');
    }
}
