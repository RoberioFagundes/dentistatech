<?php

namespace Database\Factories;

use App\Models\OrcamentoItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrcamentoItem>
 */
class OrcamentoItemFactory extends Factory
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
            'servico_id' => Servico::inRandomOrder()->first()->id ?? 1,
            'quantidade' => fake()->randomNumber(2),
            'preco_unitario' => fake()->randomFloat(2, 10, 100),
        ];
    }
}
