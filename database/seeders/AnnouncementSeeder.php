<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 8 realistic HOA announcement records for Springdale Village.
 *
 * Audience breakdown:
 *   All (5) – notices relevant to every member
 *   Residents (2) – resident-specific reminders
 *   Board Members (1) – internal board-only memo
 *
 * Status breakdown:
 *   Published (6) – live and visible
 *   Draft (1)     – pending review before release
 *   Expired (1)   – past its expires_at date
 */
class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();
        $president = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $secretary = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();

        $announcements = $this->announcements($admin->id, $president->id, $secretary->id);

        foreach ($announcements as $data) {
            Announcement::create($data);
        }

        $this->command->info('  ✔ Seeded ' . Announcement::count() . ' announcements.');
    }

    /** @return array<int, array<string, mixed>> */
    private function announcements(int $adminId, int $presidentId, int $secretaryId): array
    {
        return [
            // ──────────────────────────────────────────────────────────────────
            // 1. PINNED – Annual General Assembly 2026
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $presidentId,
                'title'        => 'Annual General Assembly 2026 – June 14, Multipurpose Hall',
                'body'         => "Dear Homeowners and Residents of Springdale Village,\n\n"
                    . "The Board of Directors cordially invites all homeowners to attend the "
                    . "Annual General Assembly (AGA) for Fiscal Year 2026.\n\n"
                    . "📅 Date: Saturday, June 14, 2026\n"
                    . "🕐 Time: 2:00 PM – 5:00 PM\n"
                    . "📍 Venue: Springdale Village Multipurpose Hall\n\n"
                    . "Agenda:\n"
                    . "1. Call to Order and Quorum Check\n"
                    . "2. Approval of Minutes – AGA 2025\n"
                    . "3. President's Annual Report\n"
                    . "4. Treasurer's Financial Report (FY 2025 Audited)\n"
                    . "5. Presentation of FY 2026 Budget\n"
                    . "6. Election of Board Members (2 seats)\n"
                    . "7. Open Forum\n\n"
                    . "Attendance is mandatory for all registered homeowners. Proxy forms are available at "
                    . "the HOA office and must be submitted no later than June 10, 2026.\n\n"
                    . "For inquiries, contact the HOA office at admin@springdale-hoa.ph or call +63 49 511 0001.\n\n"
                    . "Respectfully,\nRodrigo B. Aquino\nHOA President, Springdale Village",
                'audience'     => AnnouncementAudience::All,
                'is_pinned'    => true,
                'published_at' => '2026-05-20 08:00:00',
                'expires_at'   => '2026-06-14 23:59:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 2. PUBLISHED – Scheduled Water Interruption
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $adminId,
                'title'        => 'Scheduled Water Interruption – May 27, 2026 (7 AM – 5 PM)',
                'body'         => "Dear Springdale Village Residents,\n\n"
                    . "We have been officially notified by Manila Water / LAGUNA WATER that a scheduled "
                    . "water service interruption will be implemented in our subdivision:\n\n"
                    . "📅 Date: Wednesday, May 27, 2026\n"
                    . "🕖 Time: 7:00 AM – 5:00 PM (approx. 10 hours)\n"
                    . "📍 Affected Area: All blocks of Springdale Village\n\n"
                    . "Reason: Replacement of aging main distribution valve along Biñan-Sta. Rosa Road.\n\n"
                    . "We strongly advise all households to store sufficient water prior to the interruption. "
                    . "Water service will be gradually restored starting 5:00 PM and full pressure may take "
                    . "an additional 1–2 hours to normalize.\n\n"
                    . "We apologize for the inconvenience. For urgent concerns, please call the HOA office.\n\n"
                    . "Springdale Village HOA Administration",
                'audience'     => AnnouncementAudience::All,
                'is_pinned'    => true,
                'published_at' => '2026-05-25 06:00:00',
                'expires_at'   => '2026-05-27 20:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 3. PUBLISHED – Monthly HOA Dues Deadline Reminder (Residents)
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $adminId,
                'title'        => 'Reminder: May 2026 HOA Monthly Dues – Deadline May 31',
                'body'         => "Dear Residents,\n\n"
                    . "This is a friendly reminder that the May 2026 monthly HOA dues are due on or before "
                    . "May 31, 2026.\n\n"
                    . "💳 Accepted Payment Methods:\n"
                    . "• Cash – HOA Office (Mon–Sat, 8:00 AM – 5:00 PM)\n"
                    . "• Check – payable to 'Springdale Village HOA'\n"
                    . "• Bank Transfer – BDO Savings Account No. 004-748-012345 (Springdale Village HOA)\n"
                    . "• GCash – 0917-100-2001 (Ma. Lourdes C. Santos, Treasurer)\n\n"
                    . "⚠️ Late Fee Policy: A 2% monthly late fee is applied to unpaid dues beginning June 1, 2026. "
                    . "Consistent non-payment may result in restricted access to HOA amenities and escalation of "
                    . "account to the HOA legal counsel.\n\n"
                    . "Please bring your official HOA receipt or present your bank transaction slip for "
                    . "manual validation. Official receipts will be issued within 3 business days.\n\n"
                    . "Thank you for your prompt settlement.\n\n"
                    . "Ma. Lourdes C. Santos\nHOA Treasurer, Springdale Village",
                'audience'     => AnnouncementAudience::Residents,
                'is_pinned'    => false,
                'published_at' => '2026-05-21 08:00:00',
                'expires_at'   => '2026-05-31 23:59:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 4. PUBLISHED – Approved Renovation & Construction Guidelines 2026
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $presidentId,
                'title'        => 'Updated Renovation & Construction Guidelines – Effective June 1, 2026',
                'body'         => "Dear Homeowners,\n\n"
                    . "The Board of Directors is pleased to announce the approval of the updated "
                    . "Renovation and Construction Guidelines effective June 1, 2026. Key changes include:\n\n"
                    . "1. All renovation work must secure an HOA Renovation Permit at least 14 days before "
                    . "commencement. The previous 7-day lead time has been extended.\n\n"
                    . "2. Construction hours are strictly limited to Monday–Saturday, 8:00 AM – 5:00 PM. "
                    . "No work is permitted on Sundays, official public holidays, and from December 23 to January 2.\n\n"
                    . "3. Construction waste must be disposed of within 48 hours from the end of each workday. "
                    . "Dumping of construction debris on common areas carries a ₱5,000 fine per incident.\n\n"
                    . "4. Structural modifications (fence height, second-floor additions, covered decks) require "
                    . "submission of Architectural Plans signed by a licensed engineer or architect.\n\n"
                    . "The full updated guidelines are available at the HOA office and on the document portal. "
                    . "Please review before commencing any home improvement work.\n\n"
                    . "Rodrigo B. Aquino\nHOA President",
                'audience'     => AnnouncementAudience::All,
                'is_pinned'    => false,
                'published_at' => '2026-05-18 09:00:00',
                'expires_at'   => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 5. PUBLISHED – Waste Collection Schedule Change (Residents)
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $adminId,
                'title'        => 'Waste Collection Schedule Change Effective June 1, 2026',
                'body'         => "Dear Residents,\n\n"
                    . "Please be informed that the Biñan City MENRO (Municipal Environment and Natural Resources "
                    . "Office) has updated the waste collection schedule for our subdivision starting June 1, 2026.\n\n"
                    . "🗑️ New Collection Schedule:\n"
                    . "• Biodegradable Waste: Tuesday and Friday\n"
                    . "• Non-Biodegradable / Recyclables: Wednesday\n"
                    . "• Residual Waste: Monday and Thursday\n"
                    . "• Special / Bulky Waste: By appointment – call HOA office or MENRO hotline 049-511-0099\n\n"
                    . "Collection time: 6:00 AM – 9:00 AM. Please have your segregated waste bins placed "
                    . "outside your gate by 5:45 AM on collection days.\n\n"
                    . "Failure to segregate waste is a violation of RA 9003 (Ecological Solid Waste Management Act) "
                    . "and HOA rules. First-time violators will receive a written warning; repeat offenders will be "
                    . "fined ₱500 per incident.\n\n"
                    . "Thank you for keeping Springdale Village clean!\n\n"
                    . "HOA Administration",
                'audience'     => AnnouncementAudience::Residents,
                'is_pinned'    => false,
                'published_at' => '2026-05-22 07:00:00',
                'expires_at'   => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 6. PUBLISHED – Street Light Repair Completion Notice
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $adminId,
                'title'        => 'Street Light Repairs Completed – Narra Street and Rosal Street',
                'body'         => "Dear Residents,\n\n"
                    . "We are pleased to announce that the street light repair work along Narra Street "
                    . "(Block 3) and Rosal Street (Block 4) has been completed as of May 19, 2026.\n\n"
                    . "A total of 6 sodium vapor lamps were replaced with new LED flood lights (6500K, 50W), "
                    . "providing brighter and more energy-efficient illumination throughout the affected areas.\n\n"
                    . "The HOA would like to thank residents for their patience during the 2-week repair period "
                    . "and to acknowledge the quick response of our contracted maintenance team, "
                    . "R.B. Macaraeg General Services.\n\n"
                    . "If you notice any street light issues in your area, please report them to the HOA office "
                    . "or submit a maintenance request through the HOA portal.\n\n"
                    . "HOA Administration",
                'audience'     => AnnouncementAudience::All,
                'is_pinned'    => false,
                'published_at' => '2026-05-19 17:00:00',
                'expires_at'   => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 7. DRAFT – Security Protocol Update (pending board review)
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $secretaryId,
                'title'        => '[DRAFT] Updated Visitor & Vehicle Entry Protocol – For Board Review',
                'body'         => "Draft Notice for Board Approval:\n\n"
                    . "Subject: Updated Security and Visitor Entry Protocol\n\n"
                    . "Effective July 1, 2026, the following updated entry protocols will be enforced at the "
                    . "Springdale Village main gate:\n\n"
                    . "1. All visitors must present a valid government-issued ID upon entry.\n"
                    . "2. Delivery riders and utility personnel (MERALCO, PLDT, etc.) must be verified via "
                    . "intercom with the homeowner before gate entry is granted.\n"
                    . "3. All vehicles entering after 10:00 PM must register at the guardhouse logbook.\n"
                    . "4. Overnight visitor vehicles must be pre-registered at the HOA office.\n\n"
                    . "NOTE: This draft is pending board approval at the June 2026 meeting. Do not publish.",
                'audience'     => AnnouncementAudience::BoardMembers,
                'is_pinned'    => false,
                'published_at' => null,
                'expires_at'   => null,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 8. EXPIRED – Summer Pool Hours (past expires_at)
            // ──────────────────────────────────────────────────────────────────
            [
                'author_id'    => $adminId,
                'title'        => 'Extended Pool Hours – Holy Week 2026 (April 5–12)',
                'body'         => "Dear Residents,\n\n"
                    . "In celebration of Holy Week 2026, the HOA is pleased to announce extended swimming pool "
                    . "operating hours:\n\n"
                    . "📅 April 5 – April 12, 2026 (Holy Week)\n"
                    . "🏊 Pool Hours: 6:00 AM – 8:00 PM (Extended by 2 hours)\n\n"
                    . "Normal operating hours (6:00 AM – 6:00 PM) resume on April 13, 2026.\n\n"
                    . "Pool rules remain in effect. Children under 12 must be accompanied by an adult at all times. "
                    . "Glassware and outside food are not permitted in the pool area.\n\n"
                    . "Enjoy the summer break!\n\n"
                    . "HOA Administration",
                'audience'     => AnnouncementAudience::Residents,
                'is_pinned'    => false,
                'published_at' => '2026-04-01 08:00:00',
                'expires_at'   => '2026-04-12 23:59:00',
            ],
        ];
    }
}
