<?php

namespace Database\Factories;

use App\Models\Prontuario;
use App\Models\Procedimento;
use App\Models\Paciente;
use App\Models\Agendamento;
use App\Models\Dentista;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prontuario>
 */
class ProntuarioFactory extends Factory
{
    protected $model = Prontuario::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'queixa_principal' => fake()->optional()->sentence(),
            'historia_clinica' => fake()->optional()->paragraph(),
            'diagnostico'      => fake()->optional()->sentence(),
            'evolucao'         => fake()->optional()->paragraph(),
            'observacoes'      => fake()->optional()->text(255),
            
            // Relacionamentos Opcionais (Conforme seu esquema)
            'procedimento_id'  => fake()->boolean(70) ? Procedimento::factory() : null,
            'paciente_id'      => fake()->boolean(80) ? Paciente::factory() : null,
            'agendamento_id'   => fake()->boolean(60) ? Agendamento::factory() : null,
            'dentista_id'      => fake()->boolean(70) ? Dentista::factory() : null,
            'user_id'          => fake()->boolean(50) ? User::factory() : null,
        ];
    }
}