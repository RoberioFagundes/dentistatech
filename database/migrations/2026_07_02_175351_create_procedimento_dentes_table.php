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
        Schema::create('procedimento_dentes', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_dente')->required();
            $table->foreignId('procedimento_id')->references('id')->on('procedimentos')->required();
            $table->foreignId('atendimento_id')->references('id')->on('atendimentos')->required();
            $table->text('observacao')->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->required();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimento_dentes');
    }
};
