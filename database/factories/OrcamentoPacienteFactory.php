<?php

namespace Database\Factories;

use App\Models\OrcamentoPaciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrcamentoPaciente>
 */
class OrcamentoPacienteFactory extends Factory
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
            'orcamento_id' => Orcamento::inRandomOrder()->first()->id ?? 1,
            'paciente_id' => Paciente::inRandomOrder()->first()->id ?? 1,
            'data' => fake()->date(),
        ];
    }
}
