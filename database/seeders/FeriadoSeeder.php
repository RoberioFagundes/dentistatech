<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feriado;

class FeriadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Certifique-se de que este código esteja exatamente dentro destas chaves
        Feriado::create([
            'titulo' => 'Natal',
            'data' => '2026-12-25',
            'bloqueia_agenda' => 1,
            'recorrente_anual' => 1,
        ]);
    }
}
