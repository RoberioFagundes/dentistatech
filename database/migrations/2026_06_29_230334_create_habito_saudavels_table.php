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
        Schema::create('habito_saudavels', function (Blueprint $table) {
            $table->id();
            $table->string('frequencia_escovacao', 550)->nullable()->comment('Frequência com que o paciente realiza a escovação dentária diariamente.');
            $table->string('uso_fio_dental', 550)->nullable()->comment('Indica se o paciente utiliza fio dental.');
            $table->string('habito_fumo', 550)->nullable()->comment('Indica se o paciente tem o hábito de fumar.');
            $table->string('atividade_fisica', 550)->nullable()->comment('Indica se o paciente pratica atividade física.');
            $table->string('qualidade_sono', 550)->nullable()->comment('indica a qualidade do sono do paciente.');
            $table->unsignedBigInteger('paciente_id')->nullable()->comment('ID do paciente ao qual o hábito saudável pertence.');
            $table->text('observacoes_estilo_vida', 550)->nullable()->comment('Observações estilo de vida sobre o hábito saudável do paciente.');
            $table->unsignedBigInteger('user_id')->nullable();;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('paciente_id')
                ->references('id')
                ->on('pacientes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habito_saudavels');
    }
};
