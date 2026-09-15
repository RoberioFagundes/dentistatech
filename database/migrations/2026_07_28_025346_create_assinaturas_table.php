<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Cria SOMENTE a tabela assinaturas
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('plano_id')->default(1);
            $table->string('asaas_id')->nullable()->unique();
            $table->enum('status', ['pendente', 'ativa', 'vencida', 'cancelada'])->default('pendente');
            $table->date('inicio');
            $table->date('fim');
            $table->timestamps();
        });

        // ❌ REMOVI o bloco que alterava clinicas — campos já existem!
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
        // ❌ Também não apaga os campos da clinicas, pois já existiam antes
    }
};