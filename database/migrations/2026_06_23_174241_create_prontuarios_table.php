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
        Schema::create('prontuarios', function (Blueprint $table) {
            $table->id();
            
            // Campos clínicos textuais
            $table->string('queixa_principal', 255)->nullable();
            $table->string('historia_clinica', 255)->nullable(); 
            $table->string('diagnostico', 255)->nullable();
            $table->string('evolucao', 255)->nullable();
            $table->string('observacoes', 255)->nullable();

            // Chaves Estrangeiras do Relacionamento usando o padrão simplificado do Laravel
            $table->foreignId('procedimento_id')
                ->nullable()
                ->constrained('procedimentos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('paciente_id')
                ->nullable()
                ->constrained('pacientes')
                ->onDelete('cascade') // Se o paciente for excluído, o prontuário dele também deve ser
                ->onUpdate('cascade');

            $table->foreignId('agendamento_id')
                ->nullable()
                ->constrained('agendamentos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('dentista_id')
                ->nullable()
                ->constrained('dentistas')
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
        Schema::dropIfExists('prontuarios');
    }
};