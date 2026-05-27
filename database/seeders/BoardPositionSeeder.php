<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BoardPositionTitle;
use App\Models\BoardPosition;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds the current Board of Directors term (FY 2024–2026) for Springdale Village.
 *
 * All three board members were elected at the July 2024 AGA for a 2-year term
 * ending June 30, 2026. The term expiry aligns with the upcoming election seeded
 * in BoardElectionSeeder.
 */
class BoardPositionSeeder extends Seeder
{
    public function run(): void
    {
        $aquino   = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $santos   = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();
        $bautista = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();

        $positions = [
            [
                'user_id'    => $aquino->id,
                'position'   => BoardPositionTitle::President,
                'term_start' => '2024-07-01',
                'term_end'   => '2026-06-30',
                'is_active'  => true,
            ],
            [
                'user_id'    => $santos->id,
                'position'   => BoardPositionTitle::Treasurer,
                'term_start' => '2024-07-01',
                'term_end'   => '2026-06-30',
                'is_active'  => true,
            ],
            [
                'user_id'    => $bautista->id,
                'position'   => BoardPositionTitle::Secretary,
                'term_start' => '2024-07-01',
                'term_end'   => '2026-06-30',
                'is_active'  => true,
            ],
        ];

        foreach ($positions as $data) {
            BoardPosition::create($data);
        }

        $this->command->info('  ✔ Seeded ' . BoardPosition::count() . ' board positions (FY 2024–2026 term).');
    }
}
