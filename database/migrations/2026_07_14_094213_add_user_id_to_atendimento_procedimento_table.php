<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atendimento_procedimento', function (Blueprint $table) {
            // Adiciona o campo DEPOIS de procedimento_id, permite nulo para não quebrar registros antigos
            $table->unsignedBigInteger('user_id')
                  ->nullable()
                  ->after('procedimento_id');

            // Cria a ligação com a tabela users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Se o usuário for apagado, mantém o registro
        });
    }

    public function down(): void
    {
        Schema::table('atendimento_procedimento', function (Blueprint $table) {
            // Remove a chave estrangeira primeiro
            $table->dropForeign(['user_id']);
            // Depois remove o campo
            $table->dropColumn('user_id');
        });
    }
};