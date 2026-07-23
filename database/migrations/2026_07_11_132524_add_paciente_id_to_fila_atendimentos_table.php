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
        Schema::table('fila_atendimentos', function (Blueprint $table) {
            // Cria a coluna permitindo nulos temporariamente para não travar
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fila_atendimentos', function (Blueprint $table) {
            // Remove a chave estrangeira antes de remover a coluna
            $table->dropForeign(['paciente_id']);
            $table->dropColumn('paciente_id');
        });
    }
};
