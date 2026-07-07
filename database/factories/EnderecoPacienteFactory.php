<?php

namespace Database\Factories;

use App\Models\EnderecoPaciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnderecoPaciente>
 */
class EnderecoPacienteFactory extends Factory
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
            'rua' => fake()->streetName(),
            'numero' => fake()->buildingNumber(),
            'bairro' => fake()->neighborhood(),
            'cidade' => fake()->city(),
            'estado' => fake()->state(),
            'cep' => fake()->postcode(),
            'paciente_id' => Paciente::inRandomOrder()->first()->id ?? 1,
        ];
    }
}
