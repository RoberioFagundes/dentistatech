<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orcamento_pacientes', function (Blueprint $table) {
            $table->dropColumn(['identificacao_dente', 'queixa']);
        });
    }

    public function down(): void
    {
        Schema::table('orcamento_pacientes', function (Blueprint $table) {
            // Caso precise reverter, recria as colunas
            $table->string('identificacao_dente', 45)->nullable();
            $table->string('queixa', 255)->nullable();
        });
    }
};