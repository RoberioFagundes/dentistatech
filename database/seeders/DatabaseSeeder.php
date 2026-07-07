<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dentista; // <--- Adicione esta linha!
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Agora o PHP reconhece a classe Dentista
        Paciente::factory(20)->create();
        ContatoDentista::factory(20)->create();
        EnderecoPaciente::factory(20)->create();
        Agendamento::factory(20)->create();
        
        Dentista::factory(20)->create();
        ContatoDentista::factory(20)->create();
        OrcamentoPaciente::factory(20)->create();
        OrcamentoItem::factory(20)->create();
        Pagamento::factory(20)->create();
        Procedimento::factory(20)->create();
        ProcedimentoDente::factory(20)->create();
        Prontuario::factory(20)->create();
        Remacacao::factory(20)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}