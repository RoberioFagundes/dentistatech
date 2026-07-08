<?php

namespace Database\Factories;

use App\Models\Pagamento;
use App\Models\OrcamentoPaciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pagamento>
 */
class PagamentoFactory extends Factory
{
    protected $model = Pagamento::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'valor_pago'            => fake()->randomFloat(2, 50, 5000),
            'forma_pagamento'       => fake()->randomElement(['Cartão de Crédito', 'Cartão de Débito', 'PIX', 'Dinheiro', 'Boleto']),
            'status_pagamento'      => fake()->randomElement(['PENDENTE', 'PAGO', 'CANCELADO', 'ESTORNADO']),
            'data_pagamento'        => fake()->optional()->dateTime(),
            'observacao'            => fake()->optional()->sentence(),
            
            // Relacionamentos
            'orcamento_paciente_id' => OrcamentoPaciente::factory(),
            'user_id'               => fake()->boolean ? User::factory() : null,
        ];
    }
}