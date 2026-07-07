<?php

namespace Database\Factories;

use App\Models\ProcedimentoDente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcedimentoDente>
 */
class ProcedimentoDenteFactory extends Factory
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
            'procedimento_id' => Procedimento::inRandomOrder()->first()->id ?? 1,
            'dente_id' => Dente::inRandomOrder()->first()->id ?? 1,
            'descricao' => fake()->sentence(),  
            
        ];
    }
}
