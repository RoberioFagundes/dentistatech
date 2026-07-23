<?php

namespace Database\Factories;

use App\Models\ContatoDentista;
use App\Models\Dentista;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContatoDentista>
 */
class ContatoDentistaFactory extends Factory
{
    protected $model = ContatoDentista::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'telefone'    => fake()->phoneNumber(),
            'email'       => fake()->safeEmail(),
            'whatsapp'    => fake()->phoneNumber(),
            
            // Relacionamentos
            // Se 'user_id' pode ser nulo, usamos opcionalmente uma factory ou null
            'user_id'     => fake()->boolean ? User::factory() : null,
            
            // Relacionamento obrigatório com Dentista
            'dentista_id' => Dentista::factory(),
        ];
    }
}