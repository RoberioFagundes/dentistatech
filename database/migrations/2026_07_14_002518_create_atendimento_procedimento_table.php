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
        Schema::create('atendimento_procedimento', function (Blueprint $table) {
            $table->id(); // Opcional, mas útil se tiver campos extras
            $table->unsignedBigInteger('atendimento_id')->nullable();
            $table->unsignedBigInteger('procedimento_id')->nullable();

            // Se precisar de campos extras (ex: número do dente, valor, observação)
            $table->string('dente')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->text('observacao')->nullable();

            // Chaves estrangeiras
            $table->foreign('atendimento_id')
                ->references('id')
                ->on('atendimentos')
                ->onDelete('cascade'); // Apaga os vínculos se o atendimento for excluído

            $table->foreign('procedimento_id')
                ->references('id')
                ->on('procedimentos')
                ->onDelete('cascade');

            // Evita vínculo duplicado (mesmo atendimento + mesmo procedimento)
            $table->unique(['atendimento_id', 'procedimento_id', 'dente']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimento_procedimento');
    }
};
