<?php

namespace Database\Factories;

use App\Models\Agendamento;
use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\Atendimento;
use App\Models\Procedimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agendamento>
 */
class AgendamentoFactory extends Factory
{
    protected $model = Agendamento::class;

    public function definition(): array
    {
        return [
            'data'           => fake()->date(),
            'hora_inicio'    => fake()->time('H:i:s'),
            'hora_fim'       => fake()->time('H:i:s'),
            'status'         => fake()->randomElement(['AGENDADO', 'CONFIRMADO', 'EM_ATENDIMENTO', 'CONCLUIDO', 'CANCELADO']),
            'observacao'     => fake()->optional()->text(45),
            
            // Relacionamentos: assume-se que as factories desses modelos existem
            'dentista_id'    => Dentista::factory(),
            'paciente_id'    => Paciente::factory(),
            'atendimento_id' => Atendimento::factory(),
            'procedimento_id'=> Procedimento::factory(),
            'user_id'        => User::factory(),
        ];
    }
}