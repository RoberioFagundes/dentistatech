<?php

namespace Database\Factories;

use App\Models\Paciente;
use App\Models\User;
use App\Models\Dentista;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paciente>
 */
class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'nome'            => fake()->firstName(),
            'sobrenome'       => fake()->lastName(),
            'cpf' => fake()->optional()->numerify('###########'), // 11 dígitos
            'rg'  => fake()->optional()->numerify('#########'),  // 9 dígitos
            'sexo'            => fake()->randomElement(['Masculino', 'Feminino', 'Outro']),
            'data_nascimento' => fake()->date(),

            // Relacionamentos opcionais
            'user_id'         => fake()->boolean ? User::factory() : null,
            'dentista_id'     => fake()->boolean ? Dentista::factory() : null,
        ];
    }
}
