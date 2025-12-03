<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => User::factory(),
            'professional_id' => User::factory(),
            'service_type' => fake()->randomElement(['Enfermagem', 'Fisioterapia', 'Consulta Geral', 'Apoio Domiciliário']),
            'report' => fake()->optional(0.3)->paragraph(), // 30% de hipótese de ter relatório
            'date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'time' => fake()->time('H:i'),
            'location' => fake()->address(),
            'price' => fake()->randomFloat(2, 25, 150),
            'status' => fake()->randomElement(['Pending', 'Accepted', 'Completed', 'Cancelled']),
        ];
    }
}
