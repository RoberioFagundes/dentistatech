<?php

namespace Database\Factories;

use App\Models\OrcamentoPaciente;
use App\Models\Paciente;
use App\Models\Atendimento;
use App\Models\Procedimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrcamentoPaciente>
 */
class OrcamentoPacienteFactory extends Factory
{
    protected $model = OrcamentoPaciente::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'identificacao_dente' => fake()->optional()->bothify('##'),
            'queixa'              => fake()->optional()->sentence(),
            'validade'            => fake()->optional()->date(),
            'valor'               => fake()->randomFloat(2, 50, 2000),
            'total_orcamento'     => fake()->randomFloat(2, 100, 5000),
            
            // Relacionamentos Obrigatórios
            'paciente_id'         => Paciente::factory(),
            'atendimento_id'      => Atendimento::factory(),
            'procedimento_id'     => Procedimento::factory(),
            
            // Relacionamento Opcional
            'user_id'             => fake()->boolean ? User::factory() : null,
        ];
    }
}