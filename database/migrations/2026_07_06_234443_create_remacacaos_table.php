<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('remacacaos', function (Blueprint $table) {
            $table->id();
                        
            // Relacionamento com o agendamento original
            $table->foreignId('agendamento_id')->constrained('agendamentos')->onDelete('cascade');
            
            // Dados da nova remarcação
            $table->dateTime('data_anterior'); // Para histórico
            $table->dateTime('data_nova');     // A data para onde foi movido
            
            // Motivo da mudança
            $table->string('motivo')->nullable();
            
            // Quem realizou a ação
            $table->foreignId('user_id')->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remacacaos');
    }
};
