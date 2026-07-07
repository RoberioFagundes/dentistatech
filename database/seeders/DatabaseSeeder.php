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
        Dentista::factory(20)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}