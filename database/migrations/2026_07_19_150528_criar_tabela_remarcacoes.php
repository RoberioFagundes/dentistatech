<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('remarcacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agendamento_id');
            $table->datetime('data_anterior');
            $table->datetime('data_nova');
            $table->string('motivo', 255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Índices e chaves estrangeiras
            $table->foreign('agendamento_id')
                ->references('id')
                ->on('agendamentos')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('remarcacoes');
    }
};

