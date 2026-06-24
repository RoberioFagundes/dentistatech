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
        Schema::create('habito_bucals', function (Blueprint $table) {
            $table->id();
            $table->string('fumante');
            $table->string('frequenciaBebida');
            $table->string('praticaAtividadeFisica');
            $table->enum('respiracao', ['boca', 'nariz']);
            $table->string('frequenciaDorCabeca');
            $table->enum('rangeDente', ['sim', 'nao']);
            $table->enum('dor_mandibula', ['sim', 'nao']);
            $table->enum('sangramentoGengival', ['sim', 'nao']);
            $table->string('habitos')->nullable();
            $table->string('aparelho')->nullable();
            $table->string('medoDentista')->nullable();
            $table->string('AnsiedadeDentista')->nullable();
            $table->string('ultimaVisitaDentista')->nullable();
            $table->text('declaracao')->nullable();
            $table->unsignedBigInteger('paciente_id')->nullable();
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
        Schema::dropIfExists('habito_bucals');
    }
};
