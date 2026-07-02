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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id('id');
            $table->date('data')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->enum('status', ['AGENDADO', 'CONFIRMADO', 'EM_ATENDIMENTO', 'CONCLUIDO', 'CANCELADO', 'FALTOU', 'REAGENDADO'])->nullable();
            $table->string('observacao', 45)->nullable();

            // Chaves estrangeiras dos outros relacionamentos (Dentista e Paciente)
            $table->unsignedBigInteger('dentista_id')->nullable();
            $table->unsignedBigInteger('paciente_id')->nullable();

            // Chave Estrangeira do Atendimento
            $table->unsignedBigInteger('atendimento_id')->nullable();
            $table->foreign('atendimento_id')
                ->references('id')
                ->on('atendimentos')
                ->onDelete('restrict') // impede apagar o atendimento se houver agendamento
                ->onUpdate('cascade');

            $table->unsignedBigInteger('procedimento_id')->nullable();
            $table->foreign('procedimento_id')
                ->references('id')
                ->on('procedimentos')
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
        Schema::dropIfExists('agendamentos');
    }
};
