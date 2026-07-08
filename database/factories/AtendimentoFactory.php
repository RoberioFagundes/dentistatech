<?php

namespace Database\Factories;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\Agendamento;
use App\Models\ProcedimentoDente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Atendimento>
 */
class AtendimentoFactory extends Factory
{
    protected $model = Atendimento::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'queixa_principal'     => fake()->optional()->sentence(),
            'diagnostico'          => fake()->optional()->paragraph(),
            'tratamento_executado' => fake()->optional()->paragraph(),
            'observacoes_clinicas' => fake()->optional()->text(),
            
            // Relacionamentos Opcionais
            'paciente_id'           => fake()->boolean(80) ? Paciente::factory() : null,
            'dentista_id'           => fake()->boolean(80) ? Dentista::factory() : null,
            'agendamento_id'        => fake()->boolean(60) ? Agendamento::factory() : null,
            'procedimento_dente_id' => fake()->boolean(50) ? ProcedimentoDente::factory() : null,
            'user_id'               => fake()->boolean(50) ? User::factory() : null,
        ];
    }
}