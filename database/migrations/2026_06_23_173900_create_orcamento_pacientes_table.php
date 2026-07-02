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
        Schema::create('orcamento_pacientes', function (Blueprint $table) {
            // Chave primária padrão
            $table->id(); 
            
            // Campos extraídos do seu modelo de dados original
            $table->string('identificacao_dente', 45)->nullable();
            $table->string('queixa', 255)->nullable(); // Aumentado para 255 para dar mais espaço
            $table->string('validade', 45)->nullable();
            
            // Campos financeiros
            $table->decimal('valor', 10, 2)->nullable();
            $table->decimal('total_orcamento', 10, 2)->nullable();
            
            // Chaves Estrangeiras usando a sintaxe moderna (Segura e limpa)
            // Isso substitui a necessidade de criar a coluna e depois a regra separada
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreignId('atendimento_id')
                  ->constrained('atendimentos')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreignId('procedimento_id')
                  ->constrained('procedimentos')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

                  $table->unsignedBigInteger('user_id')->nullable();;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamento_pacientes');
    }
};