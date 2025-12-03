<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'profile_photo' => null,
            'user_type' => fake()->randomElement(['patient', 'companion', 'medical_assistant', 'nurse', 'doctor']),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'tax_id' => fake()->numerify('#########'), // 9 dígitos 
            'social_security_number' => fake()->numerify('###########'), // 11 dígitos
        ];
    }
}
