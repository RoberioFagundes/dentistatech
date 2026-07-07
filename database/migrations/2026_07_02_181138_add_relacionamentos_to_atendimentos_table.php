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
        Schema::table('atendimentos', function (Blueprint $table) {
            // Adiciona as chaves estrangeiras logo após o campo 'observacoes_clinicas'
            $table->foreignId('paciente_id')->nullable()->after('observacoes_clinicas')->constrained('pacientes')->onDelete('set null');
            $table->foreignId('dentista_id')->nullable()->after('paciente_id')->constrained('dentistas')->onDelete('set null');
            $table->foreignId('agendamento_id')->nullable()->after('dentista_id')->constrained('agendamentos')->onDelete('set null');
            $table->foreignId('procedimento_dente_id')->nullable()->after('agendamento_id')->constrained('procedimento_dentes')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            //
            // Remove as chaves estrangeiras primeiro
            $table->dropForeign(['paciente_id']);
            $table->dropForeign(['dentista_id']);
            $table->dropForeign(['agendamento_id']);
            $table->dropForeign(['procedimento_dente_id']);

            // Depois remove as colunas do banco
            $table->dropColumn([
                'paciente_id', 
                'dentista_id', 
                'agendamento_id', 
                'procedimento_dente_id'
            ]);
        });
    }
};
