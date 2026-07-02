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
        Schema::create('historico_bucals', function (Blueprint $table) {
            $table->id();
            $table->date('ultima_consulta')->nullable()->comment('Data da última consulta odontológica do paciente registrada no histórico bucal.  ');
            $table->text('motivo_consulta_atual', 550)->nullable()->comment('Motivo da última consulta odontológica do paciente registrada no histórico bucal.  ');
            $table->text('sangramento_gengival', 550)->nullable()->comment('Indica se o paciente apresenta sangramento gengival.  ');
            $table->text('sensibilidade_dental', 550)->nullable()->comment('Indica se o paciente apresenta sensibilidade dental.  ');
            $table->text('uso_proteses_aparelho', 550)->nullable()->comment('Indica se o paciente utiliza proteses ou aparelhos ortodonticos.  ');
            $table->text('tipo_aparelho_ou_protese', 550)->nullable()->comment('Observações gerais sobre o histórico bucal do paciente.  ');
            $table->text('tratamento_bucal_anterior', 550)->nullable()->comment('Observações gerais sobre o histórico bucal do paciente.  ');
            $table->text('observacoes_bucal_adicionais', 550)->nullable()->comment('Observações adicionais sobre o histórico bucal do paciente.  ');
            $table->unsignedBigInteger('paciente_id')->nullable()->comment('ID do paciente ao qual o histórico bucal pertence.  ');
            $table->foreign('paciente_id')
                ->references('id')
                ->on('pacientes')
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
        Schema::dropIfExists('historico_bucals');
    }
};
