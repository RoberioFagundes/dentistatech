<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// IMPORTAÇÕES NECESSÁRIAS
use App\Models\Paciente;
use App\Models\ContatoDentista;
use App\Models\EnderecoPaciente;
use App\Models\Agendamento;
// ... importe todos os outros modelos que você está usando aqui

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Paciente::factory(20)->create();
        ContatoDentista::factory(20)->create();
        EnderecoPaciente::factory(20)->create();
        Agendamento::factory(20)->create();
    }
}