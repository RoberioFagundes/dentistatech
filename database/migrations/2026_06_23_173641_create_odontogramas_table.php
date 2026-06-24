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
        Schema::create('odontogramas', function (Blueprint $table) {
            $table->id();
            // Campos extraídos do seu modelo relacional
            $table->string('odontogramacol', 45)->nullable(); // Campo padrão do Workbench, pode remover se não usar
            $table->string('dente', 45)->nullable(); // Ex: '11', '12', '21' (Numeração internacional)
            $table->string('face', 45)->nullable(); // Ex: 'Mesial', 'Distal', 'Oclusal', 'Palatina', 'Vestibular'
            $table->string('condicao', 45)->nullable(); // Ex: 'Cariado', 'Restaurado', 'Ausente'
            $table->string('observacao', 255)->nullable();

            // Chaves Estrangeiras do Relacionamento
            $table->unsignedBigInteger('paciente_id')->nullable();;
            $table->unsignedBigInteger('dentista_id')->nullable();;

            // Deixamos como nullable para resolver a dependência circular com procedimentos
            $table->unsignedBigInteger('procedimento_idprocedimento')->nullable();

            // Definição das Restrições (Foreign Keys)
            $table->foreign('paciente_id')
                ->references('id')
                ->on('pacientes')
                ->onDelete('cascade') // Se o paciente for excluído, o histórico do odontograma dele também é
                ->onUpdate('cascade');

            $table->foreign('dentista_id')
                ->references('id')
                ->on('dentistas')
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
        Schema::dropIfExists('odontogramas');
    }
};
