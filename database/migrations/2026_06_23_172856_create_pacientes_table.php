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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('sobrenome');    
            $table->string('cpf')->unique('cpf')->nullable();
            $table->string('rg')->nullable();
            $table->string('sexo');
            $table->date('data_nascimento');
            $table->unsignedBigInteger('dentista_id')->nullable();
            $table->foreign('dentista_id')->references('id')->on('dentistas')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
