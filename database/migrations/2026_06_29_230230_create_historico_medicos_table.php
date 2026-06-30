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
        Schema::create('historico_medicos', function (Blueprint $table) {
            $table->id();
            $table->string('alergias')->nullable();
            $table->string('medicacaoEmUso')->nullable();
            $table->string('condicaoCronica')->nullable();
            $table->string('historicoCirurgico')->nullable();
            $table->text('observacaoMedicaGeral')->nullable();
            $table->unsignedBigInteger('paciente_id')->nullable();
             // Chave estrangeira apontando para os pacientes
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
        Schema::dropIfExists('historico_medicos');
    }
};
