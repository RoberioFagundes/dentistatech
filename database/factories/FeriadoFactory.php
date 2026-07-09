<?php

namespace Database\Factories;

use App\Models\Feriado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feriado>
 */
class FeriadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3),
            'data' => fake()->date(),
            'bloqueia_agenda' => fake()->boolean(),
            'recorrente_anual' => fake()->boolean(),
            'user_id' => null, // Ou \App\Models\User::factory(),
        ];
    }
}
