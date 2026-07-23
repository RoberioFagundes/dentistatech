<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('procedimento_dentes', function (Blueprint $table) {
            // Primeiro remove a chave estrangeira antiga
            $table->dropForeign(['atendimento_id']);

            // Adiciona novamente com regra de exclusão automática
            $table->foreign('atendimento_id')
                ->references('id')
                ->on('atendimentos')
                ->onDelete('cascade'); // ✅ Apaga procedimentos quando o atendimento for apagado
        });
    }

    public function down()
    {
        Schema::table('procedimento_dentes', function (Blueprint $table) {
            // Desfaz a alteração se precisar reverter a migração
            $table->dropForeign(['atendimento_id']);
            $table->foreign('atendimento_id')
                ->references('id')
                ->on('atendimentos');
        });
    }
};

