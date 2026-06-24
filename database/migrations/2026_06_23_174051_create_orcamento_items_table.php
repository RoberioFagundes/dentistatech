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
        Schema::create('orcamento_items', function (Blueprint $table) {
        $table->id();
        $table->string('dente', 45)->nullable();
        $table->string('face', 45)->nullable();
        $table->string('quantidade', 45)->nullable();
        $table->string('status', 45)->nullable();

        // Vinculação exata com a chave primária que definimos na tabela pai
        $table->foreignId('orcamento_paciente_id')
              ->constrained('orcamento_pacientes') 
              ->onDelete('cascade')
              ->onUpdate('cascade');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamento_items');
    }
};