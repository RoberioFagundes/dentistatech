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
        Schema::table('fila_atendimentos', function (Blueprint $table) {
        $table->tinyInteger('prioridade')
              ->default(0)
              ->after('status_fila');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('fila_atendimentos', function (Blueprint $table) {
        $table->dropColumn('prioridade');
        });
    }
};
