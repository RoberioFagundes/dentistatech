<?php

namespace Database\Factories;

use App\Models\EnderecoDentistaAtendimento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnderecoDentistaAtendimento>
 */
class EnderecoDentistaFactory extends Factory
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
            'rua' => fake()->streetName(),
            'numero' => fake()->buildingNumber(),
            'bairro' => fake()->neighborhood(),
            'cidade' => fake()->city(),
            'estado' => fake()->state(),
            'cep' => fake()->postcode(),
            'dentista_id' => Dentista::inRandomOrder()->first()->id ?? 1,
        ];
    }
}
