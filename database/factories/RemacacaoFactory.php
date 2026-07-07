<?php

namespace Database\Factories;

use App\Models\Remacacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Remacacao>
 */
class RemacacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'paciente_id' => Paciente::inRandomOrder()->first()->id ?? 1,
            'descricao' => fake()->sentence(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            
        ];
    }
}
