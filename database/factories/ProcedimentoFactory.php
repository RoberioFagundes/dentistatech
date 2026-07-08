<?php

namespace Database\Factories;

use App\Models\Procedimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procedimento>
 */
class ProcedimentoFactory extends Factory
{
    protected $model = Procedimento::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'nome'            => fake()->randomElement(['Limpeza', 'Restauração', 'Canal', 'Extração', 'Clareamento', 'Implante']),
            'descricao'       => fake()->optional()->sentence(),
            'valor'           => fake()->randomFloat(2, 50, 1500),
            'duracao_minutos' => fake()->randomElement([30, 45, 60, 90]),
            'ativo'           => fake()->boolean(90), // 90% de chance de estar ativo
            
            // Relacionamento opcional
            'user_id'         => fake()->boolean ? User::factory() : null,
        ];
    }
}