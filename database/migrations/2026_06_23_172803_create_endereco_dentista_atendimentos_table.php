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
        Schema::create('endereco_dentista_atendimentos', function (Blueprint $table) {
            $table->id();
            $table->string('cep');
            $table->string('país');
            $table->string('estado');
            $table->string('cidade');
            $table->string('bairro');
            $table->string('rua');
            $table->string('numero');
            $table->unsignedBigInteger('dentista_id');
            $table->foreign('dentista_id')->references('id')->on('dentistas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endereco_dentista_atendimentos');
    }
};
