<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Amenity>
 */
class AmenityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                  => fake()->words(2, true) . ' Area',
            'description'           => fake()->sentence(),
            'location'              => 'Ground Floor',
            'capacity'              => 50,
            'fee_per_hour'          => null,
            'security_deposit'      => null,
            'monthly_booking_limit' => null,
            'is_active'             => true,
        ];
    }

    public function withFee(float $feePerHour = 500.00, float $deposit = 1000.00): static
    {
        return $this->state([
            'fee_per_hour'     => $feePerHour,
            'security_deposit' => $deposit,
        ]);
    }

    public function withMonthlyLimit(int $limit): static
    {
        return $this->state(['monthly_booking_limit' => $limit]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
