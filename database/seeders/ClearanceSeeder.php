<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ClearancePurpose;
use App\Enums\ClearanceStatus;
use App\Models\Clearance;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 3 HOA Clearance records for Springdale Village covering the three
 * clearance purposes and a spread of statuses to demonstrate the workflow.
 *
 * Status distribution:
 *   Issued   (1) – Sale clearance already processed and released
 *   Pending  (1) – Rental clearance freshly submitted, awaiting review
 *   Rejected (1) – General clearance denied due to outstanding dues
 */
class ClearanceSeeder extends Seeder
{
    public function run(): void
    {
        $secretary = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();
        $president = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();

        $clearances = [
            // ── 1. ISSUED – B3-L02 Emmanuel Soriano requesting sale clearance ─
            [
                'unit'             => 'B3-L02',
                'requester_email'  => 'emmanuel.soriano@gmail.com',
                'issued_by'        => $secretary->id,
                'purpose'          => ClearancePurpose::Sale,
                'status'           => ClearanceStatus::Issued,
                'notes'            => 'Clearance requested in connection with the sale of B3-L02 to prospective '
                    . 'buyer Mr. Percival A. Domingo (Cavite resident). Property was verified to have no '
                    . 'outstanding HOA dues, no open violation cases, no pending maintenance liens, '
                    . 'and all registered vehicles and pets are on record. '
                    . 'Board approved on April 28, 2026 per Resolution No. 2026-04-009. '
                    . 'Clearance certificate issued on April 30, 2026, valid for 90 days from issuance.',
                'rejection_reason' => null,
                'valid_until'      => '2026-07-29',
                'issued_at'        => '2026-04-30 10:30:00',
                'rejected_at'      => null,
            ],

            // ── 2. PENDING – B2-L04 Roberto Castillo requesting rental clearance
            [
                'unit'             => 'B2-L04',
                'requester_email'  => 'roberto.castillo@gmail.com',
                'issued_by'        => null,
                'purpose'          => ClearancePurpose::Rental,
                'status'           => ClearanceStatus::Pending,
                'notes'            => 'Homeowner intends to lease out B2-L04 to Ms. Shiela Mae B. Florendo '
                    . '(monthly rate ₱22,500) starting July 1, 2026. HOA Clearance required under '
                    . 'Springdale Village House Rules Section 2.4 (Tenant Registration and Clearance) '
                    . 'before any tenancy agreement is signed. All HOA dues are current as of May 2026. '
                    . 'Submitted: May 22, 2026. Board review scheduled for June 2026 regular meeting.',
                'rejection_reason' => null,
                'valid_until'      => null,
                'issued_at'        => null,
                'rejected_at'      => null,
            ],

            // ── 3. REJECTED – B4-L03 Corazon Aguilar requesting general clearance
            [
                'unit'             => 'B4-L03',
                'requester_email'  => 'corazon.aguilar@gmail.com',
                'issued_by'        => $president->id,
                'purpose'          => ClearancePurpose::General,
                'status'           => ClearanceStatus::Rejected,
                'notes'            => 'Clearance requested for submission to Biñan City Business Permit Office '
                    . 'as supporting document for DTI business name registration. '
                    . 'During review, the following outstanding matters were found that prevent issuance:\n'
                    . '(1) Open violation case VN-2026-05-004 (Unauthorized Commercial Activity); '
                    . '(2) HOA dues arrears of ₱3,600 covering March to May 2026 (3 months). '
                    . 'Homeowner was notified by email and official written notice on May 18, 2026. '
                    . 'Clearance may be re-applied for once the violation is resolved and dues are settled.',
                'rejection_reason' => 'Outstanding HOA dues (₱3,600 for March–May 2026) and open violation '
                    . 'case VN-2026-05-004 (Unauthorized Commercial Activity at B4-L03) must be fully '
                    . 'resolved before a clearance can be issued. Please settle dues at the HOA office '
                    . 'and await disposition of the violation case.',
                'valid_until'      => null,
                'issued_at'        => null,
                'rejected_at'      => '2026-05-18 09:00:00',
            ],
        ];

        foreach ($clearances as $c) {
            $property  = Property::where('unit_number', $c['unit'])->firstOrFail();
            $requester = User::where('email', $c['requester_email'])->firstOrFail();

            Clearance::create([
                'property_id'      => $property->id,
                'requested_by'     => $requester->id,
                'issued_by'        => $c['issued_by'],
                'purpose'          => $c['purpose'],
                'status'           => $c['status'],
                'notes'            => $c['notes'],
                'rejection_reason' => $c['rejection_reason'],
                'valid_until'      => $c['valid_until'],
                'issued_at'        => $c['issued_at'],
                'rejected_at'      => $c['rejected_at'],
            ]);
        }

        $this->command->info('  ✔ Seeded ' . Clearance::count() . ' HOA clearance records.');
    }
}
