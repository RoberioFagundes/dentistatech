<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->enum('status', [
                'AGENDADO',
                'EM_ATENDIMENTO',
                'ATENDIDO', // ✅ Adicionei o valor que você usa
                'CANCELADO'
            ])->default('AGENDADO')->change();
        });
    }

    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->enum('status', ['AGENDADO', 'EM_ATENDIMENTO', 'CANCELADO'])->default('AGENDADO')->change();
        });
    }
};
