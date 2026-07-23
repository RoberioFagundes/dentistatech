<?php

namespace Database\Factories;

use App\Models\EnderecoPaciente;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnderecoPaciente>
 */
class EnderecoPacienteFactory extends Factory
{
    protected $model = EnderecoPaciente::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'cep'         => fake()->postcode(),
            'pais'        => 'Brasil',
            'estado'      => fake()->stateAbbr(),
            'cidade'      => fake()->city(),
            'bairro'      => fake()->word(),
            'rua'         => fake()->streetName(),
            'numero'      => fake()->buildingNumber(),
            
            // Relacionamentos com suporte a valor nulo (conforme seu esquema)
            'user_id'     => fake()->boolean ? User::factory() : null,
            'paciente_id' => fake()->boolean ? Paciente::factory() : null,
        ];
    }
}