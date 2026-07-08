<?php

namespace Database\Factories;

use App\Models\ContatoPaciente;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContatoPaciente>
 */
class ContatoPacienteFactory extends Factory
{
    /**
     * O modelo associado a esta factory.
     */
    protected $model = ContatoPaciente::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'telefone'    => fake()->phoneNumber(),
            'whatsapp'    => fake()->cellphone(),
            
            // Campos que aceitam NULL
            'email'       => fake()->optional()->safeEmail(),
            'site'        => fake()->optional()->url(),
            
            // Relacionamentos: gera um novo registro ou retorna null aleatoriamente
            'user_id'     => fake()->boolean ? User::factory() : null,
            'paciente_id' => fake()->boolean ? Paciente::factory() : null,
        ];
    }
}