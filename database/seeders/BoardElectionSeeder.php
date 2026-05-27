<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BoardPositionTitle;
use App\Enums\ElectionStatus;
use App\Enums\NominationStatus;
use App\Models\BoardElection;
use App\Models\ElectionNomination;
use App\Models\ElectionVote;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 2 Board of Directors election records for Springdale Village.
 *
 * Election 1 (certified) – FY 2024–2026 election held at the July 2024 AGA.
 *   Resulted in the current board: Aquino (President), Santos (Treasurer),
 *   Bautista (Secretary). Full nominations, votes, and certification seeded.
 *
 * Election 2 (nominations_open) – FY 2026–2028 election for the upcoming
 *   June 14, 2026 AGA. Two seats up for election (President, Treasurer).
 *   Nominations already submitted; voting not yet open.
 */
class BoardElectionSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();
        $aquino    = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $santos    = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();
        $bautista  = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();

        $residents = User::where('role', 'resident')->get()->keyBy('email');

        // ══════════════════════════════════════════════════════════════════════
        // ELECTION 1 – FY 2024–2026 (Certified) – the past election
        // ══════════════════════════════════════════════════════════════════════
        $pastElection = BoardElection::create([
            'title'               => 'Board of Directors Election – FY 2024–2026 Term',
            'description'         => 'Election of three (3) Board of Directors seats for the Fiscal Year 2024–2026 '
                . 'two-year term. Conducted during the Annual General Assembly on July 13, 2024. '
                . 'All registered homeowners in good standing as of June 30, 2024 were eligible to vote. '
                . 'Positions to be filled: President, Treasurer, Secretary.',
            'status'              => ElectionStatus::Certified,
            'nomination_deadline' => '2024-07-06',
            'voting_open_at'      => '2024-07-13 14:00:00',
            'voting_close_at'     => '2024-07-13 17:00:00',
            'created_by'          => $admin->id,
        ]);

        // ── Nominations for past election ─────────────────────────────────────
        // President seat
        $nomAquinoPresident = ElectionNomination::create([
            'election_id'         => $pastElection->id,
            'nominee_id'          => $aquino->id,
            'nominated_by'        => $residents['carlos.reyes@gmail.com']->id,
            'position_value'      => BoardPositionTitle::President,
            'candidate_statement' => 'I, Rodrigo B. Aquino, am honored to accept the nomination for HOA President. '
                . 'With 12 years of residency in Springdale Village and a background in civil engineering, '
                . 'I am committed to transparent governance, timely infrastructure maintenance, and '
                . 'strengthening our community ties. My priorities: (1) complete the clubhouse renovation, '
                . '(2) implement online dues payment, (3) upgrade the subdivision CCTV network.',
            'status'              => NominationStatus::Accepted,
        ]);

        $nomDianaPresident = ElectionNomination::create([
            'election_id'         => $pastElection->id,
            'nominee_id'          => $residents['diana.navarro@gmail.com']->id,
            'nominated_by'        => $residents['felicidad.espiritu@gmail.com']->id,
            'position_value'      => BoardPositionTitle::President,
            'candidate_statement' => 'Diana F. Navarro, CPA, has been a homeowner since 2017. '
                . 'She previously served as Finance Committee member and brings financial discipline '
                . 'and audit experience to the HOA. Her platform: zero-arrears collections through '
                . 'structured payment plans and digitized HOA records management.',
            'status'              => NominationStatus::Accepted,
        ]);

        // Treasurer seat
        $nomSantosTreasurer = ElectionNomination::create([
            'election_id'         => $pastElection->id,
            'nominee_id'          => $santos->id,
            'nominated_by'        => $bautista->id,
            'position_value'      => BoardPositionTitle::Treasurer,
            'candidate_statement' => 'Ma. Lourdes C. Santos has been a licensed accountant for 18 years '
                . 'and has served as HOA Treasurer since 2022. She has maintained 95%+ collection rates '
                . 'and built the HOA reserve fund to ₱892,750. She stands for fiscal prudence, timely '
                . 'financial reporting, and full transparency in all HOA expenditures.',
            'status'              => NominationStatus::Accepted,
        ]);

        // Secretary seat
        $nomBautistaSecretary = ElectionNomination::create([
            'election_id'         => $pastElection->id,
            'nominee_id'          => $bautista->id,
            'nominated_by'        => $aquino->id,
            'position_value'      => BoardPositionTitle::Secretary,
            'candidate_statement' => 'Jose T. Bautista, retired government clerk and 15-year Springdale Village '
                . 'resident. Served as HOA Secretary since 2020 with a perfect record of meeting minutes '
                . 'and correspondence. His platform: digital document management, faster clearance processing, '
                . 'and direct communication channels between the board and homeowners.',
            'status'              => NominationStatus::Accepted,
        ]);

        $nomCarlosSecretary = ElectionNomination::create([
            'election_id'         => $pastElection->id,
            'nominee_id'          => $residents['carlos.reyes@gmail.com']->id,
            'nominated_by'        => $residents['benjamin.magno@gmail.com']->id,
            'position_value'      => BoardPositionTitle::Secretary,
            'candidate_statement' => null,
            'status'              => NominationStatus::Withdrawn,
        ]);

        // ── Votes for past election (conducted on July 13, 2024) ───────────
        $votersForPresident = [
            'mariagrace.delacruz@gmail.com'  => $nomAquinoPresident->id,
            'eduardo.lopez@yahoo.com'         => $nomAquinoPresident->id,
            'josephine.garcia@gmail.com'      => $nomDianaPresident->id,
            'ramon.torres@outlook.com'        => $nomAquinoPresident->id,
            'cristina.villanueva@gmail.com'   => $nomAquinoPresident->id,
            'antonio.hernandez@gmail.com'     => $nomAquinoPresident->id,
            'analucia.mendoza@yahoo.com'      => $nomDianaPresident->id,
            'roberto.castillo@gmail.com'      => $nomAquinoPresident->id,
            'emmanuel.soriano@gmail.com'      => $nomAquinoPresident->id,
            'teresita.ramos@yahoo.com'        => $nomDianaPresident->id,
            'felicidad.espiritu@gmail.com'    => $nomDianaPresident->id,
            'manuel.pascual@outlook.com'      => $nomAquinoPresident->id,
            'corazon.aguilar@gmail.com'       => $nomDianaPresident->id,
            'benjamin.magno@gmail.com'        => $nomAquinoPresident->id,
            'rosario.bersamin@yahoo.com'      => $nomAquinoPresident->id,
        ];

        $castAt = \Carbon\Carbon::parse('2024-07-13 14:05:00');
        foreach ($votersForPresident as $email => $nominationId) {
            ElectionVote::create([
                'election_id'    => $pastElection->id,
                'voter_id'       => $residents[$email]->id,
                'nomination_id'  => $nominationId,
                'position_value' => BoardPositionTitle::President,
                'cast_at'        => $castAt->toDateTimeString(),
            ]);
            $castAt->addMinutes(rand(1, 4));
        }

        // Everyone voted unanimously for Santos (Treasurer) and Bautista (Secretary)
        $castAt = \Carbon\Carbon::parse('2024-07-13 14:35:00');
        foreach (array_keys($votersForPresident) as $email) {
            ElectionVote::create([
                'election_id'    => $pastElection->id,
                'voter_id'       => $residents[$email]->id,
                'nomination_id'  => $nomSantosTreasurer->id,
                'position_value' => BoardPositionTitle::Treasurer,
                'cast_at'        => $castAt->toDateTimeString(),
            ]);
            ElectionVote::create([
                'election_id'    => $pastElection->id,
                'voter_id'       => $residents[$email]->id,
                'nomination_id'  => $nomBautistaSecretary->id,
                'position_value' => BoardPositionTitle::Secretary,
                'cast_at'        => $castAt->addSeconds(30)->toDateTimeString(),
            ]);
            $castAt->addMinutes(rand(1, 3));
        }

        // ══════════════════════════════════════════════════════════════════════
        // ELECTION 2 – FY 2026–2028 (nominations_open) – upcoming AGA 2026
        // ══════════════════════════════════════════════════════════════════════
        $upcomingElection = BoardElection::create([
            'title'               => 'Board of Directors Election – FY 2026–2028 Term',
            'description'         => 'Election for two (2) Board of Directors seats up for the FY 2026–2028 '
                . 'two-year term. To be conducted at the Annual General Assembly on June 14, 2026, 2:00 PM, '
                . 'HOA Multipurpose Hall. Incumbent Treasurer (Santos) and Secretary (Bautista) terms expire '
                . 'June 30, 2026. Homeowners in good standing (dues current, no open violations) are eligible. '
                . 'Nomination deadline: June 7, 2026. Voting to open at AGA on June 14, 2026.',
            'status'              => ElectionStatus::NominationsOpen,
            'nomination_deadline' => '2026-06-07',
            'voting_open_at'      => '2026-06-14 14:30:00',
            'voting_close_at'     => '2026-06-14 17:00:00',
            'created_by'          => $admin->id,
        ]);

        // Treasurer seat nominations
        ElectionNomination::create([
            'election_id'         => $upcomingElection->id,
            'nominee_id'          => $santos->id,
            'nominated_by'        => $aquino->id,
            'position_value'      => BoardPositionTitle::Treasurer,
            'candidate_statement' => 'I am seeking re-election as HOA Treasurer for a second term. '
                . 'During my current term, I improved the collection rate from 87% to over 95%, '
                . 'implemented structured overdue payment plans, and grew our reserve fund from '
                . '₱410,000 to ₱892,750. I commit to introducing online dues payment via GCash/Maya '
                . 'and completing the FY 2027 budget digitization project.',
            'status'              => NominationStatus::Accepted,
        ]);

        ElectionNomination::create([
            'election_id'         => $upcomingElection->id,
            'nominee_id'          => $residents['diana.navarro@gmail.com']->id,
            'nominated_by'        => $residents['felicidad.espiritu@gmail.com']->id,
            'position_value'      => BoardPositionTitle::Treasurer,
            'candidate_statement' => 'As CPA and Finance Committee member for 2 years, I have reviewed '
                . 'every monthly financial statement and annual audit. I believe the HOA can reduce '
                . 'operating costs by 12% through competitive vendor bidding and bulk purchasing. '
                . 'I will introduce a quarterly budget variance report distributed to all homeowners.',
            'status'              => NominationStatus::Accepted,
        ]);

        // Secretary seat nominations
        ElectionNomination::create([
            'election_id'         => $upcomingElection->id,
            'nominee_id'          => $bautista->id,
            'nominated_by'        => $santos->id,
            'position_value'      => BoardPositionTitle::Secretary,
            'candidate_statement' => 'Seeking re-election as HOA Secretary. Over my current term I have '
                . 'digitized all HOA records back to 2019, reduced clearance processing time from '
                . '10 to 3 working days, and implemented a cloud backup for meeting minutes and resolutions. '
                . 'For the next term: electronic voting for non-AGA resolutions and a mobile HOA portal.',
            'status'              => NominationStatus::Accepted,
        ]);

        ElectionNomination::create([
            'election_id'         => $upcomingElection->id,
            'nominee_id'          => $residents['carlos.reyes@gmail.com']->id,
            'nominated_by'        => $residents['benjamin.magno@gmail.com']->id,
            'position_value'      => BoardPositionTitle::Secretary,
            'candidate_statement' => 'Carlos M. Reyes, civil engineer with 12 years in Springdale Village. '
                . 'Currently serving on the Safety & Security Committee, I have led the push for '
                . 'CCTV upgrades and guard station improvements. As Secretary, I will focus on '
                . 'faster resolution of homeowner complaints and a transparent violation appeal process.',
            'status'              => NominationStatus::Pending,
        ]);

        $this->command->info(sprintf(
            '  ✔ Seeded %d board elections with %d nominations and %d votes.',
            BoardElection::count(),
            ElectionNomination::count(),
            ElectionVote::count(),
        ));
    }
}
