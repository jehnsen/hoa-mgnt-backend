<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MeetingStatus;
use App\Enums\VoteStatus;
use App\Models\Meeting;
use App\Models\MeetingVote;
use App\Models\User;
use App\Models\VoteResponse;
use Illuminate\Database\Seeder;

/**
 * Seeds 4 HOA meeting records for Springdale Village along with associated
 * votes and resident vote responses.
 *
 * Meeting status breakdown:
 *   Completed (2) – Past meetings with recorded minutes
 *   Scheduled (1) – Upcoming Annual General Assembly 2026
 *   Cancelled (1) – Emergency meeting that was cancelled
 *
 * Vote status breakdown:
 *   Closed (3) – Voting finished; tallies available
 *   Open (1)   – Voting still open for the upcoming meeting
 */
class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $president = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $secretary = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();
        $admin     = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();

        $residents = User::where('role', 'resident')->get()->keyBy('email');

        // ── 1. COMPLETED – April 2026 Board Monthly Meeting ───────────────────
        $aprilBoard = Meeting::create([
            'created_by'   => $president->id,
            'title'        => 'Board of Directors Regular Monthly Meeting – April 2026',
            'description'  => 'Regular monthly meeting of the Springdale Village HOA Board of Directors. '
                . 'Agenda covers Q1 financial review, violation case dispositions, Q2 maintenance plan, '
                . 'and approval of upcoming AGA 2026 date and venue.',
            'location'     => 'HOA Function Room – Springdale Village Clubhouse',
            'scheduled_at' => '2026-04-19 14:00:00',
            'status'       => MeetingStatus::Completed,
            'agenda'       => [
                'Call to Order and Roll Call',
                'Approval of March 2026 Meeting Minutes',
                'Q1 2026 Financial Report – Treasurer\'s Presentation',
                'Violation Cases Update – Status of 10 open cases',
                'Q2 Maintenance Budget Approval',
                'AGA 2026 – Date, Venue, and Election Mechanics',
                'Other Matters',
            ],
            'minutes'      => "MINUTES OF THE BOARD OF DIRECTORS REGULAR MEETING\n"
                . "Springdale Village HOA | April 19, 2026 | 2:00 PM\n"
                . "Venue: HOA Function Room, Springdale Village Clubhouse\n\n"
                . "PRESENT: Rodrigo B. Aquino (President), Ma. Lourdes C. Santos (Treasurer), "
                . "Jose T. Bautista (Secretary)\n\n"
                . "1. CALL TO ORDER – Meeting called to order at 2:07 PM. Quorum established.\n\n"
                . "2. APPROVAL OF MINUTES – March 2026 minutes approved unanimously (3-0).\n\n"
                . "3. Q1 FINANCIAL REPORT – Treasurer reported ₱285,400 collected in Q1. "
                . "Collection rate: 94.8%. Late fee income: ₱12,600. Total expenses Q1: ₱148,200. "
                . "Current reserve fund balance: ₱892,750.\n\n"
                . "4. VIOLATIONS UPDATE – 10 open cases reviewed. 2 cases elevated to HOA legal panel. "
                . "B4-L03 commercial activity case referred to HLURB-Laguna for adjudication.\n\n"
                . "5. Q2 MAINTENANCE BUDGET – ₱180,000 budget approved unanimously. Priority items: "
                . "perimeter wall repairs (₱65,000), gate motor replacement (₱45,000), "
                . "playground equipment (₱35,000), contingency (₱35,000).\n\n"
                . "6. AGA 2026 – Set for June 14, 2026, 2:00 PM at the Multipurpose Hall. "
                . "Secretary to prepare notice of meeting. 2 board seats up for election.\n\n"
                . "7. OTHER MATTERS – Board approved partnership with DTI Negosyo Center for livelihood seminar.\n\n"
                . "Meeting adjourned at 4:15 PM.\n\n"
                . "Certified correct:\nJose T. Bautista\nHOA Secretary",
        ]);

        // Vote from April board meeting: Q2 budget approval
        $budgetVote = MeetingVote::create([
            'meeting_id'   => $aprilBoard->id,
            'created_by'   => $secretary->id,
            'question'     => 'Do you approve the Q2 2026 HOA Maintenance Budget of ₱180,000 as presented by the Treasurer?',
            'options'      => ['Approve', 'Disapprove', 'Abstain'],
            'closes_at'    => '2026-04-19 16:00:00',
            'is_anonymous' => false,
            'status'       => VoteStatus::Closed,
        ]);

        VoteResponse::insert([
            ['vote_id' => $budgetVote->id, 'user_id' => $president->id, 'selected_option' => 'Approve', 'voted_at' => '2026-04-19 15:30:00', 'created_at' => now(), 'updated_at' => now()],
            ['vote_id' => $budgetVote->id, 'user_id' => $secretary->id, 'selected_option' => 'Approve', 'voted_at' => '2026-04-19 15:31:00', 'created_at' => now(), 'updated_at' => now()],
            ['vote_id' => $budgetVote->id, 'user_id' => User::where('email', 'lourdes.santos@springdale-hoa.ph')->value('id'), 'selected_option' => 'Approve', 'voted_at' => '2026-04-19 15:32:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 2. COMPLETED – March 2026 Board Monthly Meeting ───────────────────
        $marchBoard = Meeting::create([
            'created_by'   => $president->id,
            'title'        => 'Board of Directors Regular Monthly Meeting – March 2026',
            'description'  => 'Regular monthly meeting of the HOA Board. Agenda covers February financial '
                . 'report, late fee applications, vendor contract renewals, and security protocol review.',
            'location'     => 'HOA Function Room – Springdale Village Clubhouse',
            'scheduled_at' => '2026-03-15 14:00:00',
            'status'       => MeetingStatus::Completed,
            'agenda'       => [
                'Call to Order and Roll Call',
                'Approval of February 2026 Meeting Minutes',
                'February Financial Report',
                'Late Fee Application – Q4 2025 overdue accounts',
                'Bayguard Security Contract Renewal (3-year term)',
                'Vendor Evaluation – R.B. Macaraeg General Services',
                'Open Items',
            ],
            'minutes'      => "MINUTES OF THE BOARD OF DIRECTORS REGULAR MEETING\n"
                . "Springdale Village HOA | March 15, 2026 | 2:00 PM\n\n"
                . "PRESENT: Rodrigo B. Aquino, Ma. Lourdes C. Santos, Jose T. Bautista\n\n"
                . "Key Resolutions:\n"
                . "- February collections: ₱96,200 (97.1% collection rate)\n"
                . "- Late fees applied to 3 accounts with dues unpaid since December 2025\n"
                . "- Bayguard contract renewed for 3 years at ₱28,500/month (5% increase from previous)\n"
                . "- R.B. Macaraeg General Services performance rated Satisfactory\n\n"
                . "Meeting adjourned at 3:45 PM.",
        ]);

        // Vote from March meeting: Bayguard contract renewal
        $securityVote = MeetingVote::create([
            'meeting_id'   => $marchBoard->id,
            'created_by'   => $president->id,
            'question'     => 'Shall the HOA renew the Bayguard Security Services contract for 3 years at ₱28,500/month (inclusive of the 5% rate adjustment)?',
            'options'      => ['Approve renewal', 'Reject – put to public tender', 'Abstain'],
            'closes_at'    => '2026-03-15 16:00:00',
            'is_anonymous' => false,
            'status'       => VoteStatus::Closed,
        ]);

        $treasurer = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();

        VoteResponse::insert([
            ['vote_id' => $securityVote->id, 'user_id' => $president->id, 'selected_option' => 'Approve renewal', 'voted_at' => '2026-03-15 15:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['vote_id' => $securityVote->id, 'user_id' => $treasurer->id, 'selected_option' => 'Approve renewal', 'voted_at' => '2026-03-15 15:01:00', 'created_at' => now(), 'updated_at' => now()],
            ['vote_id' => $securityVote->id, 'user_id' => $secretary->id, 'selected_option' => 'Approve renewal', 'voted_at' => '2026-03-15 15:02:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 3. SCHEDULED – Annual General Assembly 2026 (upcoming) ───────────
        $aga = Meeting::create([
            'created_by'   => $president->id,
            'title'        => 'Annual General Assembly 2026',
            'description'  => 'The Annual General Assembly (AGA) for Fiscal Year 2026. All registered homeowners '
                . 'are invited. Election of 2 board seats will be conducted. Attendance or proxy submission required.',
            'location'     => 'HOA Multipurpose Hall – Springdale Village Clubhouse',
            'scheduled_at' => '2026-06-14 14:00:00',
            'status'       => MeetingStatus::Scheduled,
            'agenda'       => [
                'Call to Order and Quorum Check',
                'Approval of AGA 2025 Minutes',
                'President\'s Annual Report – FY 2026 Highlights',
                'Treasurer\'s Report – FY 2026 (Jan–May) Financials',
                'Presentation of FY 2027 Proposed Budget',
                'Election of Board Members – 2 Vacant Seats',
                'Open Forum',
            ],
            'minutes'      => null,
        ]);

        // Open pre-AGA poll: Should the HOA raise monthly dues by 10% starting 2027?
        $duesVote = MeetingVote::create([
            'meeting_id'   => $aga->id,
            'created_by'   => $president->id,
            'question'     => 'Do you agree to increase monthly HOA dues by 10% starting January 2027 to fund the subdivision road re-asphalting project?',
            'options'      => ['Yes – I agree to the increase', 'No – I oppose the increase', 'Neutral / No preference'],
            'closes_at'    => '2026-06-13 23:59:00',
            'is_anonymous' => true,
            'status'       => VoteStatus::Open,
        ]);

        // Seed pre-vote responses from several residents
        $voterEmails = [
            'carlos.reyes@gmail.com'         => 'Yes – I agree to the increase',
            'mariagrace.delacruz@gmail.com'  => 'Yes – I agree to the increase',
            'eduardo.lopez@yahoo.com'         => 'Yes – I agree to the increase',
            'josephine.garcia@gmail.com'      => 'No – I oppose the increase',
            'cristina.villanueva@gmail.com'   => 'Yes – I agree to the increase',
            'antonio.hernandez@gmail.com'     => 'Neutral / No preference',
            'diana.navarro@gmail.com'         => 'Yes – I agree to the increase',
            'teresita.ramos@yahoo.com'        => 'No – I oppose the increase',
            'felicidad.espiritu@gmail.com'    => 'Yes – I agree to the increase',
            'benjamin.magno@gmail.com'        => 'No – I oppose the increase',
        ];

        $baseTime = \Carbon\Carbon::parse('2026-05-23 09:00:00');
        $responses = [];
        foreach ($voterEmails as $email => $option) {
            $userId = $residents[$email]?->id;
            if ($userId === null) {
                continue;
            }
            $responses[] = [
                'vote_id'         => $duesVote->id,
                'user_id'         => $userId,
                'selected_option' => $option,
                'voted_at'        => $baseTime->toDateTimeString(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
            $baseTime->addMinutes(rand(5, 45));
        }
        VoteResponse::insert($responses);

        // ── 4. CANCELLED – Emergency Water Supply Meeting ─────────────────────
        Meeting::create([
            'created_by'   => $admin->id,
            'title'        => 'Emergency Meeting – LAGUNA WATER Supply Disruption Concerns',
            'description'  => 'Emergency meeting called by the HOA President to address resident complaints '
                . 'regarding recurring low water pressure and supply interruptions in Blocks 2–4. '
                . 'LAGUNA WATER district representative was invited to attend.',
            'location'     => 'HOA Multipurpose Hall – Springdale Village Clubhouse',
            'scheduled_at' => '2026-05-10 10:00:00',
            'status'       => MeetingStatus::Cancelled,
            'agenda'       => [
                'Opening Statement – HOA President',
                'LAGUNA WATER Representative Presentation on Supply Issue',
                'Resident Q&A',
                'Agreed Action Plan and Timeline',
            ],
            'minutes'      => null,
        ]);

        $meetingCount = Meeting::count();
        $voteCount    = MeetingVote::count();
        $responseCount = VoteResponse::count();

        $this->command->info("  ✔ Seeded {$meetingCount} meetings, {$voteCount} votes, {$responseCount} vote responses.");
    }
}
