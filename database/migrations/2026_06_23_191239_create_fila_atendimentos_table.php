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
        Schema::create('fila_atendimentos', function (Blueprint $table) {
            $table->id('id');

            // Vincula a fila ao agendamento do dia
            $table->unsignedBigInteger('agendamento_id');

            // Status específico para o controle de fluxo interno da clínica
            $table->enum('status_fila', ['AGUARDANDO', 'EM_ATENDIMENTO', 'FINALIZADO', 'DESISTIU'])->default('AGUARDANDO');

            // Armazena a hora exata em que o paciente sentou na cadeira de espera (para calcular tempo de atraso)
            $table->timestamp('chegada_em')->useCurrent();
            $table->timestamp('chamado_em')->nullable(); // Hora que o dentista chamou para a sala

            // Chave estrangeira apontando para os agendamentos
            $table->foreign('agendamento_id')
                ->references('id')
                ->on('agendamentos')
                ->onDelete('cascade')
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
        Schema::dropIfExists('fila_atendimentos');
    }
};
