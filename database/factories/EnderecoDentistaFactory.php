<?php

namespace Database\Factories;

use App\Models\EnderecoDentistaAtendimento;
use App\Models\Dentista; // Importação necessária
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnderecoDentistaAtendimento>
 */
class EnderecoDentistaFactory extends Factory
{
    protected $model = EnderecoDentistaAtendimento::class;

    /**
     * Define o estado padrão do modelo.
     */
    public function definition(): array
    {
        return [
            'rua'         => fake()->streetName(),
            'numero'      => fake()->buildingNumber(),
            'bairro'      => fake()->word(), // 'neighborhood' não existe no Faker padrão
            'cidade'      => fake()->city(),
            'estado'      => fake()->stateAbbr(), // 'stateAbbr' retorna siglas (ex: SP, RJ)
            'cep'         => fake()->postcode(),
            'pais'        => 'Brasil', // Ou fake()->country()
            
            // Relacionamento: cria um novo dentista se não existir nenhum
            'dentista_id' => Dentista::factory(), 
        ];
    }
}