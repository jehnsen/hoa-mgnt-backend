<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\AmenityBlackout;
use Illuminate\Database\Seeder;

/**
 * Seeds 3 amenity blackout windows for Springdale Village.
 *
 * Blackouts represent scheduled closures for maintenance, HOA events, or
 * post-event cleanup. Amenity names must match those seeded by AmenitySeeder.
 */
class AmenityBlackoutSeeder extends Seeder
{
    public function run(): void
    {
        $pool        = Amenity::where('name', 'Swimming Pool')->firstOrFail();
        $mpHall      = Amenity::where('name', 'Multipurpose Hall')->firstOrFail();
        $bballCourt  = Amenity::where('name', 'Basketball Court')->firstOrFail();

        $blackouts = [
            // ── 1. Pool closure – monthly chemical shock treatment ───────────
            [
                'amenity_id' => $pool->id,
                'start_at'   => '2026-06-10 06:00:00',
                'end_at'     => '2026-06-12 18:00:00',
                'reason'     => 'Scheduled pool shutdown for quarterly deep chemical treatment and '
                    . 'sand filter backwash. R&B Maintenance will also inspect the pool pump motor '
                    . 'and replace the pool light fixtures. Pool will reopen June 12 at 6:00 PM '
                    . 'once water quality test results are within DOH-prescribed standards. '
                    . 'Residents are advised to use the fitness center during this period.',
            ],

            // ── 2. Multipurpose Hall – post-AGA renovation closure ───────────
            [
                'amenity_id' => $mpHall->id,
                'start_at'   => '2026-06-14 20:00:00',
                'end_at'     => '2026-06-17 08:00:00',
                'reason'     => 'Post-Annual General Assembly (AGA) 2026 cleanup and minor renovation works. '
                    . 'Scope includes replacement of two damaged floor tiles, touch-up painting, '
                    . 'aircon filter cleaning, and repositioning of the folding partition wall. '
                    . 'Contracted to R&B Maintenance per Board Resolution No. 2026-04-012. '
                    . 'Booking restrictions lifted once post-renovation inspection is signed off by the HOA Secretary.',
            ],

            // ── 3. Basketball court – inter-block tournament reservation ─────
            [
                'amenity_id' => $bballCourt->id,
                'start_at'   => '2026-06-21 06:00:00',
                'end_at'     => '2026-06-22 22:00:00',
                'reason'     => 'Exclusive reservation for the 2nd Springdale Village Inter-Block Basketball '
                    . 'Tournament (Boys Open Division). Organized by the HOA Sports Committee. '
                    . 'Court is unavailable for casual use and badminton during this period. '
                    . 'Participating teams: Block 1 Sampaguita Spikers, Block 2 Ilang-ilang Ballers, '
                    . 'Block 3 Narra Trojans, and Block 4 Rosal Warriors. '
                    . 'Residents may watch from the designated spectator area along the sideline.',
            ],
        ];

        foreach ($blackouts as $data) {
            AmenityBlackout::create($data);
        }

        $this->command->info('  ✔ Seeded ' . AmenityBlackout::count() . ' amenity blackout windows.');
    }
}
