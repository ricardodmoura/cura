<?php

namespace Database\Factories;

use App\Models\Qualification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Qualification>
 */
class QualificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->paragraph(),
            'cedula_number' => fake()->numerify('#####'),
            'document' => 'qualifications/demo_cert.pdf',
            'verification_status' => Qualification::STATUS_PENDING,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'verification_status' => Qualification::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);
    }

    public function rejected(string $reason = 'Documento ilegível.'): static
    {
        return $this->state(fn () => [
            'verification_status' => Qualification::STATUS_REJECTED,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
