<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            // Definindo a chave primária de acordo com o padrão do seu banco
            $table->id('id');

            // Campo clínico principal extraído do seu modelo original
            $table->string('queixa_principal', 255)->nullable();

            // CAMPOS ADICIONAIS SUGERIDOS (para um prontuário/atendimento completo):
            $table->text('diagnostico')->nullable();
            $table->text('tratamento_executado')->nullable();
            $table->text('observacoes_clinicas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
