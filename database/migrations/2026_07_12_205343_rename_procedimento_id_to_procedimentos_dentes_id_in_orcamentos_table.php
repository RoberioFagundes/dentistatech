<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orcamento_pacientes', function (Blueprint $table) {

            $table->unsignedBigInteger('procedimento_dentes_id')->nullable();

            $table->foreign('procedimento_dentes_id', 'fk_orcamento_proc_dentes_new')
                ->references('id')
                ->on('procedimento_dentes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orcamento_pacientes', function (Blueprint $table) {
            // Remove a constraint pelo nome criado acima
            $table->dropForeign('fk_orcamento_proc_dentes_new');
        });
    }
};