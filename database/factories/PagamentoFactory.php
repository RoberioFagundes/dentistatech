<?php

namespace Database\Factories;

use App\Models\Pagamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pagamento>
 */
class PagamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'orcamento_id' => Orcamento::inRandomOrder()->first()->id ?? 1,
            'valor' => fake()->randomFloat(2, 10, 1000),
            'data_pagamento' => fake()->date(),
            'metodo_pagamento' => fake()->randomElement(['cartao', 'boleto', 'pix']),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            
        ];
    }
}
