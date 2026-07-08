<?php

namespace Database\Factories;

use App\Models\Remacacao;
use App\Models\Agendamento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Remacacao>
 */
class RemacacaoFactory extends Factory
{
    protected $model = Remacacao::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'agendamento_id' => Agendamento::factory(),
            'data_anterior'  => fake()->dateTimeBetween('-1 month', 'now'),
            'data_nova'      => fake()->dateTimeBetween('now', '+1 month'),
            'motivo'         => fake()->optional()->sentence(),
            'user_id'        => User::factory(),
        ];
    }
}