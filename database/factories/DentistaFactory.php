<?php

namespace Database\Factories;

use App\Models\Dentista;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dentista>
 */
class DentistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

    return [
        'nome' => fake()->firstName(),
        'sobrenome' => fake()->lastName(),
        'cro' => (string) fake()->numberBetween(10000, 99999),
        'cro_uf' => fake()->randomElement($ufs),
        'especialidade' => fake()->randomElement(['Ortodontia', 'Endodontia', 'Cirurgia', 'Odontopediatria', 'Implantodontia']),
        'user_id' => 1, // Ajuste para o ID de um usuário existente na sua tabela users
    ];
    }
}
