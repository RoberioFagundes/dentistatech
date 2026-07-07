<?php

namespace Database\Factories;

use App\Models\Procedimento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procedimento>
 */
class ProcedimentoFactory extends Factory
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
            'nome' => fake()->word(),
            'descricao' => fake()->sentence(),
            'preco' => fake()->randomFloat(2, 10, 100),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            
        ];
    }
}
