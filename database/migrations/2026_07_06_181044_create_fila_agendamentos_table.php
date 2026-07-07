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
        Schema::create('fila_agendamentos', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento obrigatório
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Identifica quem adicionou
            
            // Dados do formulário
            $table->string('motivo');
            $table->integer('dias_urgencia')->default(0);
            $table->string('arquivo_path')->nullable(); // Para o anexo/documento
            $table->string('status')->default('pendente'); // ex: pendente, agendado, cancelado
            $table->text('parecer_telefonico')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fila_agendamentos');
    }
};
