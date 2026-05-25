<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 9 realistic HOA document records for Springdale Village.
 *
 * Documents represent the typical files a Philippine subdivision HOA
 * would maintain and distribute – by-laws, financials, meeting minutes,
 * notices, and forms. File paths use a logical storage structure.
 *
 * Category breakdown:
 *   Rules (2)      – By-laws and deed of restrictions
 *   Minutes (2)    – Board and general assembly minutes
 *   Financials (2) – Quarterly and annual financial reports
 *   Forms (1)      – Resident-use request forms
 *   Notices (1)    – Official notices
 *   Other (1)      – Internal reference
 *
 * Visibility:
 *   Public (7)   – Available to all authenticated residents
 *   Private (2)  – Board/admin only (is_public = false)
 */
class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();
        $president = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $secretary = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();
        $treasurer = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();

        $documents = $this->documents($admin->id, $president->id, $secretary->id, $treasurer->id);

        foreach ($documents as $data) {
            Document::create($data);
        }

        $public  = Document::where('is_public', true)->count();
        $private = Document::where('is_public', false)->count();

        $this->command->info("  ✔ Seeded " . Document::count() . " documents ({$public} public, {$private} private).");
    }

    /** @return array<int, array<string, mixed>> */
    private function documents(int $adminId, int $presidentId, int $secretaryId, int $treasurerId): array
    {
        return [
            // ──────────────────────────────────────────────────────────────────
            // 1. RULES – HOA By-Laws and Constitution (2024 Revised)
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $presidentId,
                'title'        => 'Springdale Village HOA By-Laws and Constitution (2024 Revised Edition)',
                'description'  => 'The official by-laws and constitution of Springdale Village Homeowners '
                    . 'Association as ratified during the Special General Assembly on March 15, 2024. '
                    . 'This document governs the rights, obligations, and election procedures for all '
                    . 'homeowners and the Board of Directors.',
                'category'     => DocumentCategory::Rules,
                'file_path'    => 'documents/rules/springdale-bylaws-2024.pdf',
                'file_name'    => 'Springdale-Village-HOA-By-Laws-2024-Revised.pdf',
                'file_size'    => 2_145_280,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2024-04-01 08:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 2. RULES – Deed of Restrictions (Original, 1998)
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $adminId,
                'title'        => 'Deed of Restrictions – Springdale Village (Executed 1998)',
                'description'  => 'Original Deed of Restrictions executed by the developer, Springdale '
                    . 'Development Corporation, and annotated on all Transfer Certificates of Title within '
                    . 'the subdivision. Governs land use, building construction, and property transfers. '
                    . 'Digitized from the original document filed with the Registry of Deeds, Laguna.',
                'category'     => DocumentCategory::Rules,
                'file_path'    => 'documents/rules/springdale-deed-of-restrictions-1998.pdf',
                'file_name'    => 'Springdale-Deed-of-Restrictions-1998.pdf',
                'file_size'    => 1_832_960,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2024-01-10 09:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 3. MINUTES – Board of Directors Meeting – April 2026
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $secretaryId,
                'title'        => 'Board of Directors Meeting Minutes – April 19, 2026',
                'description'  => 'Official minutes of the Springdale Village HOA Board of Directors '
                    . 'regular monthly meeting held on April 19, 2026 at the HOA Function Room. '
                    . 'Covers financial update for Q1 2026, violation case dispositions, and approval '
                    . 'of the Q2 maintenance budget.',
                'category'     => DocumentCategory::Minutes,
                'file_path'    => 'documents/minutes/board-meeting-minutes-2026-04-19.pdf',
                'file_name'    => 'Board-Meeting-Minutes-April-2026.pdf',
                'file_size'    => 548_864,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2026-04-25 10:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 4. MINUTES – Annual General Assembly 2025 Minutes
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $secretaryId,
                'title'        => 'Annual General Assembly 2025 – Official Meeting Minutes',
                'description'  => 'Certified minutes of the Springdale Village HOA Annual General Assembly '
                    . 'held on June 8, 2025. Includes the president\'s report, audited financial statements '
                    . 'FY 2024, election results for 2 board seats (Ms. Santos re-elected; Mr. Bautista '
                    . 'elected first term), and resolutions passed during the open forum.',
                'category'     => DocumentCategory::Minutes,
                'file_path'    => 'documents/minutes/aga-2025-minutes.pdf',
                'file_name'    => 'AGA-2025-Official-Minutes.pdf',
                'file_size'    => 1_024_000,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2025-07-01 08:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 5. FINANCIALS – Q1 2026 Financial Report (Public)
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $treasurerId,
                'title'        => 'Q1 2026 Financial Report (January – March 2026)',
                'description'  => 'Quarterly financial report covering HOA income and expenditures for '
                    . 'January through March 2026. Includes monthly dues collection rate, late fee income, '
                    . 'maintenance expenses, fund balances, and year-to-date comparison. '
                    . 'Presented and approved during the April 2026 Board Meeting.',
                'category'     => DocumentCategory::Financials,
                'file_path'    => 'documents/financials/springdale-q1-2026-financial-report.pdf',
                'file_name'    => 'Q1-2026-Financial-Report.pdf',
                'file_size'    => 764_928,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2026-04-25 10:30:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 6. FINANCIALS – FY 2025 Audited Financial Statements (Private)
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $treasurerId,
                'title'        => 'FY 2025 Audited Financial Statements – Internal Board Copy',
                'description'  => 'Full audited financial statements for Fiscal Year 2025 prepared by '
                    . 'external auditor Cruz & Panganiban CPAs (BOA No. 0781). Includes Balance Sheet, '
                    . 'Statement of Receipts and Disbursements, and Notes to Financial Statements. '
                    . 'Internal board copy with auditor annotations. Abridged version for residents '
                    . 'was shared during AGA 2025.',
                'category'     => DocumentCategory::Financials,
                'file_path'    => 'documents/financials/fy2025-audited-financials-internal.pdf',
                'file_name'    => 'FY2025-Audited-FS-Internal.pdf',
                'file_size'    => 3_211_264,
                'mime_type'    => 'application/pdf',
                'is_public'    => false,
                'published_at' => '2026-03-20 09:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 7. FORMS – Renovation Permit Application Form
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $adminId,
                'title'        => 'HOA Renovation / Construction Permit Application Form (2026)',
                'description'  => 'Official application form for homeowners intending to perform any renovation, '
                    . 'construction, or structural modification within their lot or unit. Must be submitted at '
                    . 'least 14 calendar days prior to commencement of work, together with the plans and '
                    . 'required clearances listed in the checklist on page 2 of this form.',
                'category'     => DocumentCategory::Forms,
                'file_path'    => 'documents/forms/renovation-permit-application-2026.pdf',
                'file_name'    => 'HOA-Renovation-Permit-Application-2026.pdf',
                'file_size'    => 312_320,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2026-06-01 00:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 8. NOTICES – RA 9904 Compliance Notice (Magna Carta for Homeowners)
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $presidentId,
                'title'        => 'RA 9904 Compliance Notice – Rights and Responsibilities of Homeowners',
                'description'  => 'Official notice summarizing the rights and obligations of HOA members '
                    . 'under Republic Act 9904 (Magna Carta for Homeowners and Homeowners Associations) '
                    . 'and its Implementing Rules and Regulations (IRR). As mandated by the HLURB/DHSUD, '
                    . 'all HOA members are required to be informed of these provisions.',
                'category'     => DocumentCategory::Notices,
                'file_path'    => 'documents/notices/ra9904-homeowners-rights-notice.pdf',
                'file_name'    => 'RA9904-Homeowners-Rights-and-Responsibilities.pdf',
                'file_size'    => 491_520,
                'mime_type'    => 'application/pdf',
                'is_public'    => true,
                'published_at' => '2024-01-15 08:00:00',
            ],

            // ──────────────────────────────────────────────────────────────────
            // 9. OTHER – HOA Emergency Contact Directory (Board Only)
            // ──────────────────────────────────────────────────────────────────
            [
                'uploaded_by'  => $adminId,
                'title'        => 'HOA Emergency Contact Directory 2026 – Internal Use Only',
                'description'  => 'Consolidated directory of emergency contacts including: HOA board officers '
                    . '(mobile and landline), Bayguard 24/7 security hotline, Biñan City fire station, '
                    . 'Biñan City police station (Station 1 – San Antonio), LAGUNA WATER emergency, '
                    . 'MERALCO emergency, Biñan City CENRO, and accredited contractors. '
                    . 'For board and security personnel use only. Do not distribute publicly.',
                'category'     => DocumentCategory::Other,
                'file_path'    => 'documents/other/hoa-emergency-contacts-2026-internal.pdf',
                'file_name'    => 'HOA-Emergency-Contacts-2026-Internal.pdf',
                'file_size'    => 204_800,
                'mime_type'    => 'application/pdf',
                'is_public'    => false,
                'published_at' => '2026-01-02 08:00:00',
            ],
        ];
    }
}
