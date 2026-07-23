<?php

namespace Database\Factories;

use App\Models\ProcedimentoDente;
use App\Models\Procedimento;
use App\Models\Atendimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcedimentoDente>
 */
class ProcedimentoDenteFactory extends Factory
{
    protected $model = ProcedimentoDente::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'numero_dente'    => fake()->numberBetween(11, 48), // Faixa comum na odontologia
            'observacao'      => fake()->optional()->text(200),
            
            // Relacionamentos Obrigatórios
            'procedimento_id' => Procedimento::factory(),
            'atendimento_id'  => Atendimento::factory(),
            'user_id'         => User::factory(),
        ];
    }
}