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
        Schema::create('hospitalizados', function (Blueprint $table) {
            $table->id();
            // Detalhes da internação
            $table->string('motivo', 255)->nullable(); // Ex: 'Cirurgia cardíaca', 'Tratamento de pneumonia'
            $table->string('ano', 4)->nullable();      // Ano em que ocorreu a hospitalização
            $table->string('tempo_internacao', 45)->nullable(); // Ex: '5 dias', '2 semanas'
            $table->text('observacoes')->nullable();

            // Chave Estrangeira vinculando ao Histórico de Saúde ou diretamente ao Paciente
            // Vinculando ao paciente costuma deixar as consultas mais rápidas:
            $table->unsignedBigInteger('paciente_id')->nullable();

            // Definição da Restrição (Foreign Key)
            $table->foreign('paciente_id')
                ->references('id')
                ->on('pacientes')
                ->onDelete('cascade') // Se o paciente for deletado, o histórico de internações some com ele
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
        Schema::dropIfExists('hospitalizados');
    }
};
