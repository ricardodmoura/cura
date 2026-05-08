<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => User::factory(),
            'professional_id' => User::factory(),
            'service_type' => fake()->randomElement(['Consulta Médica', 'Higiene', 'Penso', 'Injetáveis', 'Alimentação']),
            'report' => fake()->optional(0.3)->paragraph(),
            'date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'time' => fake()->time('H:i'),
            'location' => fake()->address(),
            'price' => fake()->randomFloat(2, 25, 150),
            // Status canónico em minúsculas; 'canceled' (single L) bate certo com o resto da app.
            'status' => fake()->randomElement(['pending', 'confirmed', 'accepted', 'completed', 'canceled']),
        ];
    }
}
