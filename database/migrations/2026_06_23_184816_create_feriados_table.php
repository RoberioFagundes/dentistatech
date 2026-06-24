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
        Schema::create('feriados', function (Blueprint $table) {
            $table->id();
            // Nome ou descrição do feriado (Ex: "Natal", "Ano Novo", "Recesso Administrativo")
            $table->string('titulo', 100);

            // A data exata do feriado
            $table->date('data');

            // Campo booleano muito útil para feriados nacionais que se repetem todo ano na mesma data
            // Se for verdadeiro (true), o sistema sabe que todo ano naquele dia a agenda deve ser bloqueada
            $table->boolean('recorrente_anual')->default(false)->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
