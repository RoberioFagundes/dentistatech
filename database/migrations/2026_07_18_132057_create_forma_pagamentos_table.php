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
        Schema::create('forma_pagamentos', function (Blueprint $table) {
            $table->id();
            // Ex: 'Pix', 'Cartão de Crédito', 'Dinheiro'
            $table->string('nome');

            // Usuário dono/criador da forma de pagamento
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Ex: 2.50 (taxa da maquininha de cartão)
            $table->decimal('taxa_desconto', 8, 2)->default(0);

            // Booleano para ativar/desativar a forma de pagamento na recepção
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forma_pagamentos');
    }
};
