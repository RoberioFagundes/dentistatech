<?php

namespace Database\Factories;

use App\Models\ContatoDentista;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContatoDentista>
 */
class ContatoDentistaFactory extends Factory
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
            'telefone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'whatsapp' => fake()->cellphoneNumber(),
            'user_id' => 1, // Ajuste conforme seu sistema de autenticação
            'dentista_id' => Dentista::inRandomOrder()->first()->id ?? 1,
        ];
    }
}
