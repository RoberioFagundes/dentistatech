<?php

namespace Database\Factories;

use App\Models\OrcamentoItem;
use App\Models\OrcamentoPaciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrcamentoItem>
 */
class OrcamentoItemFactory extends Factory
{
    protected $model = OrcamentoItem::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'dente'                => fake()->optional()->bothify('##'), // Ex: "18", "32"
            'face'                 => fake()->optional()->randomElement(['O', 'M', 'D', 'V', 'L']),
            'quantidade'           => fake()->optional()->randomDigitNotNull(),
            'status'               => fake()->optional()->randomElement(['PENDENTE', 'APROVADO', 'REPROVADO']),
            
            // Relacionamentos obrigatórios e opcionais
            'orcamento_paciente_id'=> OrcamentoPaciente::factory(),
            'user_id'              => fake()->boolean ? User::factory() : null,
        ];
    }
}