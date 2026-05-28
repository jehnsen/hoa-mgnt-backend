<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'unit_number'    => fake()->numerify('A-###'),
            'block'          => 'Block ' . fake()->numberBetween(1, 20),
            'floor'          => null,
            'street_address' => fake()->streetAddress(),
            'type'           => 'residential',
            'monthly_dues'   => 2500.00,
            'late_fee_rate'  => 0.05,
            'is_active'      => true,
            'is_delinquent'  => false,
            'delinquent_since' => null,
        ];
    }

    public function delinquent(): static
    {
        return $this->state([
            'is_delinquent'   => true,
            'delinquent_since' => now()->subMonths(3)->toDateString(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
