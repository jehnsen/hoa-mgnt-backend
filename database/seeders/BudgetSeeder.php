<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BudgetCategory;
use App\Models\HoaBudget;
use Illuminate\Database\Seeder;

/**
 * Seeds the FY 2026 Annual Budget for Springdale Village HOA.
 *
 * Budget line items cover all 8 BudgetCategory values. Amounts are realistic
 * for a ~20-unit Philippine subdivision in Biñan City, Laguna with 2 vendors
 * (security + maintenance) and 5 managed amenities.
 *
 * Total FY 2026 budget: ₱1,840,000
 * Approved at: Board Resolution No. 2026-01-001, January 18, 2026.
 */
class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Security,
                'budgeted_amount' => 342000.00,
                'notes'           => 'Bayguard Security Services contract (FY 2026–2027 renewed at ₱28,500/month × 12). '
                    . 'Covers 24/7 gate security: 2 guards per shift (day/night), roving patrol, '
                    . 'visitor logbook and vehicle access control. Approved per Board Resolution 2026-01-001.',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Maintenance,
                'budgeted_amount' => 480000.00,
                'notes'           => 'R&B Maintenance & Landscaping contract (₱25,000/month × 12 = ₱300,000) '
                    . 'plus capital repair allowance ₱180,000 covering: gate motor replacement (₱45,000), '
                    . 'perimeter wall crack repairs (₱65,000), playground equipment overhaul (₱35,000), '
                    . 'guardhouse roof replacement (₱20,000), and contingency repairs (₱15,000).',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Utilities,
                'budgeted_amount' => 210000.00,
                'notes'           => 'LAGUNA WATER billing for common-area meter (clubhouse, pool, irrigation): '
                    . 'est. ₱8,500/month (₱102,000). Meralco electricity for streetlights (21 posts × ₱380/month), '
                    . 'clubhouse common areas, and gate motor: est. ₱9,000/month (₱108,000). '
                    . 'Amounts based on FY 2025 actuals plus 5% OPEX escalation.',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Landscaping,
                'budgeted_amount' => 140000.00,
                'notes'           => 'Quarterly tree-trimming of 34 street-side trees: ₱15,000 × 4 = ₱60,000. '
                    . 'Annual common-area perimeter wall repainting: ₱85,000 (dry season, Q1 2026). '
                    . 'However, repainting was completed in March 2026 at actual cost ₱82,400. '
                    . 'Balance available for additional landscaping beautification per Landscaping Committee recommendation.',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Amenities,
                'budgeted_amount' => 180000.00,
                'notes'           => 'Swimming pool: chemical treatment (₱3,500 × 12 = ₱42,000), pump repair allowance ₱25,000. '
                    . 'Multipurpose Hall/Clubhouse: function room renovation completion ₱55,000 (new flooring + aircon). '
                    . 'Fitness center: treadmill belt replacement ₱18,000, cable machine overhaul ₱12,000. '
                    . 'Basketball court: rubberized floor re-coating ₱28,000.',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Administrative,
                'budgeted_amount' => 96000.00,
                'notes'           => 'HOA office operating expenses: office supplies (₱2,000/month), '
                    . 'photocopying/printing (₱1,500/month), postage and courier (₱800/month). '
                    . 'Legal retainer: Atty. Jose Libanan (₱3,500/month × 12 = ₱42,000). '
                    . 'Annual audit fee: ₱18,000 (Reyes & Associates CPAs, Biñan City). '
                    . 'Board of Directors representation allowance: ₱1,200/month × 12 = ₱14,400.',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Contingency,
                'budgeted_amount' => 240000.00,
                'notes'           => 'Emergency reserve and contingency fund equivalent to approximately 13% of '
                    . 'total FY 2026 budget. Per RA 9904 and HOA By-Laws Article IX, the Board may draw '
                    . 'from this fund by majority resolution for unforeseen expenses. '
                    . 'Unutilized contingency funds at year-end to be transferred to the HOA Sinking Fund.',
            ],
            [
                'fiscal_year'     => 2026,
                'category'        => BudgetCategory::Other,
                'budgeted_amount' => 152000.00,
                'notes'           => 'Community programs and events: AGA 2026 (₱35,000 – venue, catering, materials), '
                    . 'Christmas Village decor and party (₱45,000), Linggo ng Wika and Fiesta celebration (₱15,000). '
                    . 'HOA website hosting and domain renewal: ₱8,000/year. '
                    . 'DHSUD annual registration and compliance filing: ₱5,500. '
                    . 'CCTV system quarterly maintenance: ₱3,200 × 4 = ₱12,800. '
                    . 'Miscellaneous/unclassified: ₱30,700.',
            ],
        ];

        foreach ($entries as $data) {
            HoaBudget::create($data);
        }

        $total = (float) HoaBudget::where('fiscal_year', 2026)->sum('budgeted_amount');

        $this->command->info(sprintf(
            '  ✔ Seeded %d FY 2026 budget entries (total: ₱%s).',
            HoaBudget::count(),
            number_format($total, 2),
        ));
    }
}
