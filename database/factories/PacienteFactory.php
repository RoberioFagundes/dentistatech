<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paciente>
 */
class PacienteFactory extends Factory
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
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->cpf(false),
            'data_nascimento' => fake()->date(),
            'sexo' => fake()->randomElement(['M', 'F']),
            'user_id' => 1, // Ajuste conforme seu sistema de autenticação
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            
        ];
    }
}
