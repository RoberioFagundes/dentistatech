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
        Schema::create('cirurgias', function (Blueprint $table) {
            $table->id();
            // Detalhes clínicos da cirurgia pregressa
            $table->string('tipo_cirurgia', 255)->nullable(); // Ex: 'Cardíaca (Ponte de Safena)', 'Bariátrica'
            $table->string('ano', 4)->nullable();            // Ano em que foi realizada
            $table->string('complicacoes', 255)->nullable();  // Ex: 'Houve hemorragia', 'Nenhuma'
            $table->text('observacoes')->nullable();

            // Chave Estrangeira vinculando diretamente ao Paciente
            $table->unsignedBigInteger('paciente_id')->nullable();

            // Definição da Restrição (Foreign Key)
            $table->foreign('paciente_id')
                ->references('id')
                ->on('pacientes')
                ->onDelete('cascade') // Se o paciente for deletado, o histórico de cirurgias dele é excluído
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirurgias');
    }
};
