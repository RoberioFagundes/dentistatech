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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            // Informações financeiras importantes
            $table->decimal('valor_pago', 10, 2);
            $table->string('forma_pagamento', 45); // Ex: 'Dinheiro', 'Cartão de Crédito', 'Pix', 'Boleto'
            $table->enum('status_pagamento', ['PENDENTE', 'PAGO', 'CANCELADO', 'ESTORNADO'])->default('PENDENTE');
            $table->timestamp('data_pagamento')->nullable(); // Preenchida quando o dinheiro entra de fato
            $table->string('observacao', 255)->nullable(); // Para anotar parcelas, descontos, etc.

            // Chave Estrangeira vinculando o pagamento ao Orçamento aprovado
            $table->unsignedBigInteger('orcamento_paciente_id');

            // Definição da restrição (Foreign Key)
            $table->foreign('orcamento_paciente_id')
                ->references('id')
                ->on('orcamento_pacientes')
                ->onDelete('restrict') // Impede apagar o orçamento se houver histórico de pagamentos atrelado
                ->onUpdate('cascade');

                $table->unsignedBigInteger('user_id')->nullable();;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
