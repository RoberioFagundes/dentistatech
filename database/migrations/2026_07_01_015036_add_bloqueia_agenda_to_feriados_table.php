<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // <-- Ela já está importada aqui em cima corretamente
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterado de Concentrador para Blueprint
        Schema::table('feriados', function (Blueprint $table) {
            // Cria a coluna como boolean, com valor padrão true (1)
            $table->boolean('bloqueia_agenda')->default(true)->after('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Alterado de Concentrador para Blueprint
        Schema::table('feriados', function (Blueprint $table) {
            $table->dropColumn('bloqueia_agenda');
        });
    }
};