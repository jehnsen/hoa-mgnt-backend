<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ViolationCategory;
use App\Enums\ViolationStatus;
use App\Models\Property;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Violation>
 */
class ViolationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id'    => Property::factory(),
            'reported_by'    => User::factory()->boardMember(),
            'title'          => fake()->sentence(4),
            'description'    => fake()->paragraph(2),
            'category'       => ViolationCategory::Noise->value,
            'status'         => ViolationStatus::Draft->value,
            'fine_amount'    => 500.00,
            'invoice_id'     => null,
            'evidence_images' => null,
            'issued_at'      => null,
            'resolved_at'    => null,
        ];
    }

    public function issued(): static
    {
        return $this->state([
            'status'    => ViolationStatus::Issued->value,
            'issued_at' => now()->toDateTimeString(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(['status' => ViolationStatus::Draft->value]);
    }
}
