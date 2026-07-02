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
        Schema::create('procedimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            
            // Valor do procedimento (ex: 999999.99)
            $table->decimal('valor', 8, 2)->default(0.00);
            
            // Duração estimada em minutos (ex: 30, 60, 90)
            $table->integer('duracao_minutos')->default(30);
            
            // Permite desativar um procedimento sem deletar o histórico
            $table->boolean('ativo')->default(true); 
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
        Schema::dropIfExists('procedimentos');
    }
};
