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
        Schema::create('procedimentos', function (Blueprint $table) {
            $table->id();
            // Campos de dados da consulta
            $table->date('data')->nullable();
            $table->string('procedimento', 100)->nullable(); // Ex: 'Canal', 'Restabelecimento', 'Limpeza'

            // Chave Estrangeira vinculando ao Odontograma
            $table->unsignedBigInteger('odontograma_id')->nullable(); // Permite nulo caso o procedimento não esteja vinculado a um odontograma específico

            // Definição do relacionamento (Foreign Key)
            $table->foreign('odontograma_id')
                ->references('id')
                ->on('odontogramas') // ATENÇÃO: Verifique se sua migration do odontograma cria a tabela como 'odontogramas'
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimentos');
    }
};
