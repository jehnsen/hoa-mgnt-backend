<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ElectionStatus;
use App\Models\BoardElection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardElection>
 */
class BoardElectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'               => 'Board of Directors Election ' . fake()->year(),
            'description'         => fake()->sentence(),
            'status'              => ElectionStatus::Draft,
            'nomination_deadline' => now()->addDays(7)->toDateString(),
            'voting_open_at'      => now()->addDays(10)->toDateTimeString(),
            'voting_close_at'     => now()->addDays(17)->toDateTimeString(),
            'created_by'          => User::factory()->boardMember(),
        ];
    }
}
