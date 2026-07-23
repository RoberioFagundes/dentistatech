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
        Schema::table('users', function (Blueprint $table) {
            // Vínculo com a clínica
          

            // Seus campos solicitados
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('telefone', 15)->nullable();
            $table->date('data_admissao')->nullable();
            $table->boolean('ativo')->default(true); // Acesso liberado por padrão
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cpf',
                'telefone',
                'data_admissao',
                'ativo'
            ]);
        });
    }
};
