<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Nome exato do índice que apareceu na consulta: "cpf"
            $table->dropUnique('cpf');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Se precisar voltar atrás, recria a restrição
            $table->unique('cpf');
        });
    }
};