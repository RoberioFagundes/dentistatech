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
        Schema::create('historico_saudes', function (Blueprint $table) {
            $table->id();
            $table->string('doenca')->nullable();
            $table->string('medicamentos')->nullable();
            $table->string('alergias')->nullable();
            $table->string('reacaoAnestisiaLocal')->nullable();
            $table->string('reacaoAnestisiaGeral')->nullable();
            $table->string('distubio_sanguineo')->nullable();
            $table->string('dificuldadesCoagulacao')->nullable();
            $table->string('anticoagulantes')->nullable();
            $table->string('transfusao_sangue')->nullable();
            $table->string('problemasRespiratorio')->nullable();
            $table->string('problemaNeurologico')->nullable();
            $table->string('doencaInfeciosa')->nullable();
            $table->string('hospitalizado')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('historico_saudes');
    }
};
